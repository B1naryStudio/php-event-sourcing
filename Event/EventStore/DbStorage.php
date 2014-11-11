<?php

namespace EventSourcing\Event\EventStore;


class DbStorage implements StorageInterface {

    public function load($id, $fromVersion = null) {
        $fromVersion = intval($fromVersion);

        $eventsData = \Events::whereRaw('uuid = ? and version >= ?', array($id, $fromVersion))->get();

        if(count($eventsData) == 0) {
            return null;
        }

        $events = array();
        foreach($eventsData as $eventData) {
            $events[] = $eventData->event_data;
        }

        return new StreamData($eventData->uuid, $eventData->class_name, $events, $eventData->version);
    }

    public function store($id, $className, $eventData, $nextVersion, $currentVersion) {
        $lastEvent = \Events::where('uuid', ' = ', $id)->orderBy('version', 'desc')->first();

        if($lastEvent && $lastEvent->version !== $currentVersion) {
            throw new \Exception('Concurrently exception');
        }

        foreach($eventData as $event) {
            \Events::create(array(
                'uuid'       => $id,
                'class_name' => $className,
                'version'    => $nextVersion,
                'event_data' => $event
            ));

            $nextVersion++;
        }
    }

    public function contains($id) {
        return (bool) \Events::where('uuid', ' = ', $id)->count();
    }
}