<?php

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\EventManager;
use PHPExtra\EventManager\Listener\Listener;

class DummyEvent implements Event
{
    public $calls = array();
}

class DummyCancellableEvent implements Event
{
    public $events = array();
}

class DummyCancellableEvent2 extends DummyCancellableEvent
{
}

/**
 * Class InfiniteLoopCauser
 */
class InfiniteLoopCauser implements Listener
{
    /**
     * @var PHPExtra\EventManager\EventManager
     */
    protected $em;

    /**
     * @param EventManager $em
     */
    public function __construct(EventManager $em){
        $this->em = $em;
    }

    /**
     * @param DummyCancellableEvent $event
     */
    public function onDummyEvent(DummyCancellableEvent $event){
        $em = $this->em;
        $em->trigger($event);
    }
}

/**
 * Class DummyListener1
 */
class DummyListener1 implements Listener
{
    /**
     * Short desc
     * Long ident and some numeric value priority 500
     *
     * @priority                 100
     * @param \DummyCancellableEvent $event
     */
    public function onDummyEvent1(DummyCancellableEvent $event){
        $event->events[] = 'Dummy 1.2';
    }

    /**
     * @priority HIGH
     * @param \DummyCancellableEvent $event
     */
    public function onDummyEvent2(DummyCancellableEvent $event){
        $event->events[] = 'Dummy 1.1';
    }

    /**
     * @priority LOWEST
     * @param \DummyCancellableEvent $event
     */
    public function onDummyEvent3(DummyCancellableEvent $event){
        $event->events[] = 'Dummy 1.3';
    }

    /**
     * @priority -2000
     * @param \DummyCancellableEvent $event
     */
    public function onDummyEvent4(DummyCancellableEvent $event){
        $event->events[] = 'Dummy 1.4';
    }
}

/**
 * Class DummyListener2
 */
class DummyListener2 implements Listener
{
    /**
     * @priority -1000
     * @param \DummyCancellableEvent $event
     */
    public function onDummyEvent(DummyCancellableEvent $event){
        $event->events[] = 'Dummy 2 Event 1';
    }

    /**
     * @priority high
     * @param \DummyCancellableEvent2 $event
     */
    public function onDummyEvent2(DummyCancellableEvent2 $event){
        $event->events[] = 'Dummy 2 Event 2';
    }

    /**
     * @priority LOWEST
     * @param \DummyCancellableEvent2 $event
     */
    public function onDummyEvent3(DummyCancellableEvent2 $event){
        $event->events[] = 'Dummy 3 Event 2';
    }
}

/**
 * Class DummyListener3
 */
class DummyListener3 implements Listener
{
    /**
     * @priority -1000
     * @param \DummyCancellableEvent $event
     */
    private function onDummyEvent(DummyCancellableEvent $event){
        $event->events[] = 'Dummy 2 Event 1';
    }

    protected function onDummyEvent2(DummyCancellableEvent $event){
        $event->events[] = 'Dummy 2 Event 2';
    }
}