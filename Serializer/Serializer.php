<?php

namespace EventSourcing\Serializer;

class Serializer implements SerializerInterface {

    public function unserialize($data) {
        return unserialize($data);
    }

    public function serialize($object) {
        return serialize($object);
    }
}