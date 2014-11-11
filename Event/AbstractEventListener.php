<?php

namespace EventSourcing\Event;

use AbstractReadModel;

abstract class AbstractEventListener {

    /**
     * @param AbstractReadModel $model
     * @param AbstractEvent $event
     * @return AbstractReadModel
     */
    protected function _populateReadModel(AbstractReadModel $model, AbstractEvent $event) {
        foreach($event as $field => $value) {
            $model->{$field} = $value;
        }

        return $model;
    }
}
