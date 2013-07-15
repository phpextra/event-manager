<?php

use Skajdo\EventManager\AbstractCancellableEvent;
use Skajdo\EventManager\EventManager;
use Skajdo\EventManager\Listener;

class DummyCancellableEvent extends AbstractCancellableEvent
{
    public $events = array();
    public $sum = 15;
}

class DummyCancellableEvent2 extends AbstractCancellableEvent
{
    public $sum = 10;
}

/**
 * Class InfiniteLoopCauser
 */
class InfiniteLoopCauser implements Listener
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

class DummyListener1 implements Listener
{
    /**
     * Short desc
     * Long desc
     *
     * @param DummyCancellableEvent $event
     * @priority 100
     */
    public function onDummyEvent(DummyCancellableEvent $event){
        $event->sum = $event->sum * 10;
        $event->events[] = get_class($this) . ' * 10';
    }
}

class DummyListener2 implements Listener
{
    protected $sum = 0;

    /**
     * @priority -1000
     * @param DummyCancellableEvent $event
     */
    public function onDummyEvent(DummyCancellableEvent $event){
        $event->sum = $event->sum + 5;
        $event->events[] = get_class($this) . ' + 5';
        //$this->sum = $event->sum + $this->sum;
    }

    /**
     * @priority highest
     * @param \DummyCancellableEvent|\DummyCancellableEvent2 $event
     */
    public function onDummyEvent2(DummyCancellableEvent $event){
        $event->sum = $event->sum * 5;
        $event->events[] = get_class($this) . ' * 5';
    }
}