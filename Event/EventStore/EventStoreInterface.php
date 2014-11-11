<?php

namespace EventSourcing\Event\EventStore;

interface EventStoreInterface {

    public function find($uuid, $fromVersion = null);

    public function commit(EventStream $stream);
}