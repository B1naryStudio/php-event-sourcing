<?php

namespace EventSourcing\Event\HandlerLocator;

use EventSourcing\Event\EventInterface;

class EventHandlerLocator implements EventHandlerLocatorInterface {

    protected $_handlers = array();

    public function getHandlers(EventInterface $event) {
        $eventName = strtolower($event->getName());

        if(!isset($this->_handlers[$eventName])) {
            return array();
        }

        return $this->_handlers[$eventName];
    }

    public function register($handler) {
        foreach (get_class_methods($handler) as $methodName) {
            if (strpos($methodName, 'on') !== 0) {
                continue;
            }

            $eventName = strtolower(substr($methodName, 2));

            if (!isset($this->_handlers[$eventName])) {
                $this->_handlers[$eventName] = array();
            } else {
                foreach($this->_handlers[$eventName] as $existingHandler) {
                    if(get_class($existingHandler) == get_class($handler)) {
                        continue 2;
                    }
                }
            }

            $this->_handlers[$eventName][] = $handler;
        }
    }
}