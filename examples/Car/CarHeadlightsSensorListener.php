<?php

/**
 * We have a sensor module that listens to car events
 */
use Skajdo\EventManager\Listener\ListenerInterface;

class CarHeadlightsSensorListener implements ListenerInterface
{
    /**
     * Perform some logic on CarStartEvent() - for example, turn on the headlights
     *
     * @param CarStartEvent $event
     */
    public function doSomethingOn(CarStartEvent $event)
    {
        if(!$event->isCancelled()){
            if($event->getCar()->hasHeadlightsTurnedOn() != true){
                $event->getCar()->turnHeadlightsOn();
            }
        }
    }
}