<?php

namespace EventSourcing;

use EventSourcing\Event\AbstractEvent;
use EventSourcing\Event\EventStore\EventStream;

abstract class AggregateRoot {

    private $_id;
    private $_events = array();
    protected $_attributes = array();

    protected function _setId($uuid) {
        $this->_id = $uuid;
    }

    public function getId() {
        return $this->_id;
    }

    protected function apply(AbstractEvent $event) {
        $this->_executeEvent($event);
        $this->_events[] = $event;
    }

    private function _executeEvent($event) {
        $method = 'apply' . $event->getName();

        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException();
        }

        $this->$method($event);
    }

    public function loadFromEventStream(EventStream $eventStream) {
        if ($this->_events) {
            throw new \RuntimeException("AggregateRoot was already created from event stream and cannot be hydrated again.");
        }

        $this->_setId($eventStream->getUuid());

        foreach ($eventStream as $event) {
            $this->_executeEvent($event);
        }
    }

    public function pullDomainEvents() {
        $events = $this->_events;
        $this->_events = array();

        return $events;
    }

    public function __set($name, $val) {
        $this->_attributes[$name] = $val;
    }

    public function __get($name) {
        if(isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }

        return null;
    }

    public function getAttributes() {
        return $this->_attributes;
    }

    public function populateEventData(AbstractEvent $event) {
        foreach($event as $property => $value) {
            $this->$property = $value;

            if($property == 'id') {
                $this->_setId($value);
            }
        }
    }
}