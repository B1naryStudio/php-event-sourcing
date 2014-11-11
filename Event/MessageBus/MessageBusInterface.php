<?php

namespace EventSourcing\Event\MessageBus;

use EventSourcing\Event\AbstractEvent;

interface MessageBusInterface {

    public function publish(AbstractEvent $event);

}