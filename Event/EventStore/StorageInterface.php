<?php

namespace EventSourcing\Event\EventStore;

interface StorageInterface {
    public function load($id, $fromVersion = null);

    public function store($id, $className, $eventData, $nextVersion, $currentVersion);

    public function contains($id);
}
