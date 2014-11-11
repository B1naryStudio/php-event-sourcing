<?php

namespace EventSourcing\Event\EventStore;

class MemoryStorage implements StorageInterface {

    private $_streamData = array();

    public function load($id) {
        if (isset($this->_streamData[$id])) {
            return $this->_streamData[$id];
        }

        return null;
    }

    public function store($id, $className, $eventData, $nextVersion, $currentVersion) {
        if (isset($this->_streamData[$id]) && $this->_streamData[$id]->getVersion() !== $currentVersion) {
            throw new \Exception('Concurrency exception');
        }

        $this->_streamData[$id] = new StreamData($id, $className, $eventData, $nextVersion);
    }

    public function contains($id) {
        return isset($this->_streamData[$id]);
    }

}