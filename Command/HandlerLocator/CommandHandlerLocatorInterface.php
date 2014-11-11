<?php

namespace EventSourcing\Command\HandlerLocator;

use EventSourcing\Command\AbstractCommand;

interface CommandHandlerLocatorInterface {

    public function getCommandHandler(AbstractCommand $command);

}