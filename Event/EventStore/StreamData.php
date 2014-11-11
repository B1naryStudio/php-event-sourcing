<?php

namespace EventSourcing\Event\EventStore;

class StreamData {

    private $_id;
    private $_eventData;
    private $_version;
    private $_className;

    public function __construct($id, $className, $eventData, $version) {
        $this->_id = $id;
        $this->_className = $className;
        $this->_eventData = $eventData;
        $this->_version = $version;
    }

    public function getId() {
        return $this->_id;
    }

    public function getClassName() {
        return $this->_className;
    }

    public function getVersion() {
        return $this->_version;
    }

    public function getEventData() {
        return $this->_eventData;
    }
}
