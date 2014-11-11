<?php

namespace EventSourcing\Event\MessageBus;

use EventSourcing\Event\AbstractEvent;
use EventSourcing\Event\HandlerLocator\EventHandlerLocatorInterface;

class MessageBus implements MessageBusInterface {

    protected $_locator;

    public function __construct(EventHandlerLocatorInterface $eventHandlerLocator) {
        $this->_locator = $eventHandlerLocator;
    }

    public function publish(AbstractEvent $event) {
        $handlers = $this->_locator->getHandlers($event);

        foreach($handlers as $handler) {
            $this->_invokeEventHandler($handler, $event);
        }
    }

    protected function _invokeEventHandler($handler, AbstractEvent $event) {
        $method = 'on' . $event->getName();

        if(is_callable(array($handler, $method))) {
            $handler->$method($event);
        } else {
            throw new \Exception('Handler does not support this method');
        }
    }
}