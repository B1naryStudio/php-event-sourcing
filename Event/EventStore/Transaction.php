<?php

namespace EventSourcing\Event\EventStore;

class Transaction {

    private $_eventStream;
    private $_committedEvents = array();

    public function __construct(EventStream $eventStream, array $committedEvents) {
        $this->_eventStream = $eventStream;
        $this->_committedEvents = $committedEvents;
    }

    public function getEventStream() {
        return $this->_eventStream;
    }

    public function getCommittedEvents() {
        return $this->_committedEvents;
    }
}
