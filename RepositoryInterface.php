<?php

namespace EventSourcing;

interface RepositoryInterface {

    public function find($className, $uuid);

    public function save(AggregateRoot $object);
}