<?php

namespace EventSourcing\Event\EventStore\SnapshotStore;

interface SnapshotStoreInterface {

    public function load($uuid);

    public function save($uuid, $version, $data);
}