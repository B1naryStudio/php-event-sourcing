<?php

namespace EventSourcing\Command;

abstract class AbstractCommand {

    public function __construct($data = array()) {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key )) {
                $parts   = explode("\\", get_class($this));
                $command = str_replace("Command", "", end($parts));

                throw new \RuntimeException('Property ' . $key . ' is not a valid property on command ' . $command);
            }

            $this->$key = $value;
        }
    }

    public function getData()
    {
        return get_object_vars($this);
    }

}