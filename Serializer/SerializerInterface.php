<?php

namespace EventSourcing\Serializer;

interface SerializerInterface {

    public function unserialize($data);

    public function serialize($object);
}