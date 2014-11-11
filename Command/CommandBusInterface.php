<?php

namespace EventSourcing\Command;

interface CommandBusInterface {
    public function handle(AbstractCommand $command);
}