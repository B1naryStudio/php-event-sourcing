<?php

namespace EventSourcing\Command\HandlerLocator;

use EventSourcing\Command\AbstractCommand;

class CommandHandlerLocator implements CommandHandlerLocatorInterface {

    private $_handlers = array();

    public function getCommandHandler(AbstractCommand $command) {
        $commandType = get_class($command);

        if (!isset($this->_handlers[strtolower($commandType)])) {
            throw new \RuntimeException("No service registered for command type '" . $commandType . "'");
        }

        return $this->_handlers[strtolower($commandType)];
    }

    public function register($commandType, $service) {
        if (!is_object($service)) {
            throw new \RuntimeException("No valid service given for command type '" . $commandType . "'");
        }

        $this->_handlers[strtolower($commandType)] = $service;
    }
}