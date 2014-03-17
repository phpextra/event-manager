<?php

use PHPExtra\EventManager\EventManager;
use PHPExtra\EventManager\Listener\ListenerInterface;

class DummyEvent implements \PHPExtra\EventManager\Event\EventInterface
{
    public $calls = array();
}

class DummyCancellableEvent implements \PHPExtra\EventManager\Event\EventInterface
{
    public $events = array();
}

class DummyCancellableEvent2 extends DummyCancellableEvent
{
}

/**
 * Class InfiniteLoopCauser
 */
class InfiniteLoopCauser implements ListenerInterface
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
class DummyListener1 implements ListenerInterface
{
    /**
     * Short desc
     * Long desc
     *
     * @priority 100
     * @param \DummyCancellableEvent $event
     */
    public function onDummyEvent(DummyCancellableEvent $event){
        $event->events[] = 'Dummy 1';
    }
}

/**
 * Class DummyListener2
 */
class DummyListener2 implements ListenerInterface
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
}