<?php

namespace EventSourcing\Event\EventStore;

use EventSourcing\Event\AbstractEvent;

class EventStream implements \IteratorAggregate {

    private $_uuid;
    private $_events = array();
    private $_newEvents = array();
    private $_version;
    private $_class;

    public function __construct($className, $uuid, array $events = array(), $version = null) {
        $this->_class = $className;
        $this->_uuid = $uuid;
        $this->_events = $events;
        $this->_version = $version;
    }

    public function getUuid() {
        return $this->_uuid;
    }

    public function getVersion() {
        return $this->_version;
    }

    public function getClassName() {
        return $this->_class;
    }

    public function addEvents(array $events) {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
    }

    public function addEvent(AbstractEvent $event) {
        $this->_events[] = $event;
        $this->_newEvents[] = $event;
    }

    public function getIterator() {
        return new \ArrayIterator($this->_events);
    }

    public function newEvents() {
        return $this->_newEvents;
    }

    public function markNewEventsProcessed($newVersion = null) {
        $this->_version = $newVersion;
        $this->_newEvents = array();
    }
}
