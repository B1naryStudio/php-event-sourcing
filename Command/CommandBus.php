<?php

namespace EventSourcing\Command;

use EventSourcing\Command\HandlerLocator\CommandHandlerLocatorInterface;

class CommandBus implements CommandBusInterface {

    private $_locator;
    private $_commandStack = array();
    private $_executing = false;

    public function __construct(CommandHandlerLocatorInterface $locator) {
        $this->_locator = $locator;
    }

    public function handle(AbstractCommand $command) {
        $this->_commandStack[] = $command;

        if($this->_executing) {
            return;
        }

        $first = true;

        while ($command = array_shift($this->_commandStack)) {
            $this->invokeHandler($command, $first);
            $first = false;
        }
    }

    protected function invokeHandler($command, $first) {
        try {
            $this->_executing = true;

            $service = $this->_locator->getCommandHandler($command);
            $method  = $this->getHandlerMethodName($command);

            if (!method_exists($service, $method)) {
                throw new \RuntimeException("Service " . get_class($service) . " has no method " . $method . " to handle command.");
            }

            $service->$method($command);
        } catch (\Exception $e) {
            $this->_executing = false;
            $this->handleException($e, $first);
        }

        $this->_executing = false;
    }

    protected function getHandlerMethodName($command) {
        $parts = explode("\\", get_class($command));

        return lcfirst(end($parts));
    }

    protected function handleException($e, $first) {
        if($first) {
            throw $e;
        }
    }
}