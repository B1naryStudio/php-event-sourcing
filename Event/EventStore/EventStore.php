<?php

namespace EventSourcing\Event\EventStore;

use EventSourcing\Serializer\SerializerInterface;

class EventStore implements EventStoreInterface {

    private $_storage;
    private $_serializer;
    private $_eventsData = array();

    public function __construct(StorageInterface $storage, SerializerInterface $serializer) {
        $this->_storage = $storage;
        $this->_serializer = $serializer;
    }

    public function find($uuid, $fromVersion = null) {
        $streamData = $this->_storage->load($uuid, $fromVersion);

        if ($streamData === null) {
            return null;
        }

        $events = array();

        foreach ($streamData->getEventData() as $eventData) {
            $events[] = $this->_serializer->unserialize($eventData);
        }

        $eventStream = new EventStream(
            $streamData->getClassName(),
            $streamData->getId(),
            $events,
            $streamData->getVersion()
        );

        $this->_eventsData[$uuid] = $streamData->getEventData();

        return $eventStream;
    }

    public function commit(EventStream $stream) {
        $newEvents = $stream->newEvents();

        if (count($newEvents) === 0) {
            return new Transaction($stream, $newEvents);
        }

        $id = $stream->getUuid();
        $currentVersion = $stream->getVersion();
        $nextVersion = $currentVersion + 1;

        $eventData = array();

        foreach ($newEvents as $newEvent) {
            $eventData[] = $this->_serializer->serialize($newEvent);
        }

        $this->_storage->store($id, $stream->getClassName(), $eventData, $nextVersion, $currentVersion);

        $stream->markNewEventsProcessed($nextVersion);

        return new Transaction($stream, $newEvents);
    }
}