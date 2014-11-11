<?php

namespace EventSourcing\Event\HandlerLocator;

use EventSourcing\Event\EventInterface;

interface EventHandlerLocatorInterface {

    public function getHandlers(EventInterface $event);

}