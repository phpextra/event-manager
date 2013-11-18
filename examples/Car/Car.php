<?php

/**
 * Car entity
 * This is a simple example of how the event manager works
 */

use Skajdo\EventManager\EventManager;
use Skajdo\EventManager\Listener\ListenerInterface;

class Car
{
    /**
     * @var \Skajdo\EventManager\EventManager
     */
    protected $eventManager;

    /**
     * Car headlights - true = On, false = Off
     * @var bool
     */
    protected $headlights = false;

    /**
     * @param EventManager $eventManager
     */
    function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Add a listener to our car
     *
     * @param ListenerInterface $listener
     */
    public function addSensorListener(ListenerInterface $listener)
    {
        $this->eventManager->addListener($listener);
    }

    /**
     * Start the car
     * Under the hood, this method calls the event - CarStartEvent()
     *
     * @see CarStartEvent
     * @return $this
     */
    public function startEngine()
    {
        $this->eventManager->trigger(new CarStartEvent($this));
        return $this;
    }

    /**
     * Turn headlights on
     */
    public function turnHeadlightsOn()
    {
        $this->headlights = true;
    }

    /**
     * Tell if current car has its headlights on
     *
     * @return bool
     */
    public function hasHeadlightsTurnedOn()
    {
        return $this->headlights == true;
    }
}