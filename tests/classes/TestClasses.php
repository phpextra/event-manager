<?php

use Skajdo\EventManager\AbstractCancellableEvent;
use Skajdo\EventManager\EventManager;
use Skajdo\EventManager\ListenerInterface;

class DummyCancellableEvent extends AbstractCancellableEvent
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
     * @var Skajdo\EventManager\EventManager
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

        $looper = function(DummyCancellableEvent $event) use ($em, &$looper){
            $em->addListener($looper);
            $em->trigger($event);
        };
        $this->em->addListener($looper);
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