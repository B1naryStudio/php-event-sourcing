<?php

namespace EventSourcing\Event;

use IteratorAggregate;

abstract class AbstractEvent implements EventInterface, IteratorAggregate {

    protected $_name;
    protected $_aggregateId;

    public $version;

    public function __construct($data = array()) {
        foreach($data as $key => $value) {
            if(property_exists($this, $key)) {
                $this->$key = $value;
            } else {
                throw new \Exception('Invalid property');
            }
        }
    }

    /**
     * Returns name of event
     *
     * @return string
     */
    public function getName() {
        if(empty($this->_name)) {
            $class = get_class($this);

            if(substr($class, -5) === "Event") {
                $class = substr($class, 0, -5);
            }

            if(strpos($class, "\\") === false) {
                $this->_name = $class;
            }

            $parts = explode("\\", $class);
            $this->_name = end($parts);
        }

        return $this->_name;
    }

    /**
     * @param mixed $aggregateId
     * @return void
     */
    public function setAggregateId($aggregateId) {
        $this->_aggregateId = $aggregateId;
    }

    /**
     * @return mixed
     */
    public function getAggregateId() {
        return $this->_aggregateId;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this);
    }

}