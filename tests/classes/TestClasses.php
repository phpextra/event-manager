<?php

use Skajdo\EventManager\Event\CancellableEvent;
use Skajdo\EventManager\Listener\Listener;

class DummyCancellableEvent extends CancellableEvent
{
    public $events = array();
    public $sum = 15;
}

class DummyCancellableEvent2 extends CancellableEvent
{
    public $sum = 10;
}

class DummyListener1 extends Listener
{
    /**
     * Short desc
     * Long desc
     *
     * @param DummyCancellableEvent $event
     * @priority 200
     */
    public function onDummyEvent(DummyCancellableEvent $event){
        $event->sum = $event->sum * 10;
        $event->events[] = get_class($this) . ' * 10';
    }
}

class DummyListener2 extends Listener
{
    protected $sum = 0;

    /**
     * @priority 200
     * @param DummyCancellableEvent $event
     */
    public function onDummyEvent(DummyCancellableEvent $event){
        $event->sum = $event->sum + 5;
        $event->events[] = get_class($this) . ' + 5';
        //$this->sum = $event->sum + $this->sum;
    }

//    public function onDummyEvent2(DummyCancellableEvent2 $event){
//        $event->sum = 15 + 5;
//        //$this->sum = $event->sum * 2;
//    }
}