<?php

namespace EventSourcing\Event\EventStore;

use EventSourcing\Event\EventStore\SnapshotStore\SnapshotStoreInterface;
use EventSourcing\RepositoryInterface;
use EventSourcing\AggregateRoot;
use EventSourcing\Event\MessageBus\MessageBusInterface;

class EventRepository implements RepositoryInterface {

    private $_eventStore;
    private $_eventBus;
    private $_streams = array();
    private $_snapshotStore;
    private $_snapshotFrequency;

    const SNAPSHOT_FREQUENCY = 10;

    public function __construct(EventStoreInterface $eventStore, MessageBusInterface $eventBus, SnapshotStoreInterface $snapshotStore = null) {
        $this->_eventStore = $eventStore;
        $this->_eventBus = $eventBus;
        $this->_snapshotStore = $snapshotStore;
        $this->_snapshotFrequency = self::SNAPSHOT_FREQUENCY;
    }

    public function find($className, $uuid, $expectedVersion = null) {
        $fromVersion = null;
        $snapshot    = null;

        if($this->_snapshotStore) {
            $snapshot = $this->_snapshotStore->load($uuid);
            if($snapshot) {
                $fromVersion = $snapshot->version;
            }
        }

        $eventStream = $this->_eventStore->find($uuid, $fromVersion);
        if($eventStream === null) {
            return null;
        }

        $this->_streams[$uuid] = $eventStream;

        if ($expectedVersion && $eventStream->getVersion() !== $expectedVersion) {
            throw new EventRepositoryException('Concurrency exception');
        }

        if($snapshot) {
            $aggregateRoot = $this->_createFromSnapshot($snapshot);
        } else {
            $aggregateRootClass = $eventStream->getClassName();
            $aggregateRoot = new $aggregateRootClass();
        }

        $aggregateRoot->loadFromEventStream($eventStream);

        return $aggregateRoot;
    }

    public function save(AggregateRoot $object) {
        $id = $object->getId();

        if (!isset($this->_streams[$id])) {
            $this->_streams[$id] = new EventStream(
                get_class($object),
                $id
            );
        }

        $eventStream = $this->_streams[$id];
        $eventStream->addEvents($object->pullDomainEvents());

        $transaction = $this->_eventStore->commit($eventStream);

        foreach ($transaction->getCommittedEvents() as $event) {
            $event->setAggregateId($object->getId());
            $event->setVersion($eventStream->getVersion());

            $this->_eventBus->publish($event);
        }

        $this->_saveSnapshot($object, $transaction->getEventStream()->getVersion());
    }

    public function setSnapshotFrequency($frequency) {
        if(is_int($frequency) && $frequency > 0) {
            $this->_snapshotFrequency = $frequency;
        }
    }

    private function _createFromSnapshot($snapshot) {
        return unserialize($snapshot->data);
    }

    private function _saveSnapshot(AggregateRoot $object, $version) {
        if($version % $this->_snapshotFrequency == 0) {
            $this->_snapshotStore->save($object->getId(), $version, serialize($object));
        }
    }
}

class EventRepositoryException extends \Exception {}