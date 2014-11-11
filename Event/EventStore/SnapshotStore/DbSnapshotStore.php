<?php

namespace EventSourcing\Event\EventStore\SnapshotStore;

class DbSnapshotStore implements SnapshotStoreInterface {

    public function load($uuid) {
        return \Snapshot::where('uuid', '=', $uuid)->orderBy('version', 'desc')->first();
    }

    public function save($uuid, $version, $data) {
        return \Snapshot::create(array(
            'uuid'    => $uuid,
            'version' => $version,
            'data'    => $data
        ));
    }
}