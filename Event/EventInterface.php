<?php

namespace EventSourcing\Event;

interface EventInterface  {

    public function getName();

    public function setAggregateId($aggregateId);

    public function getAggregateId();

}