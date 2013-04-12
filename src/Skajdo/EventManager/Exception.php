<?php

namespace Skajdo\EventManager;

use Skajdo\EventManager\Event\EventInterface;
use Skajdo\EventManager\Listener\ListenerInterface;

/**
 * Event manager exception
 *
 * @author      Jacek Kobus
 */
class Exception extends \Exception
{
    /**
     * @var ListenerInterface
     */
    protected $listener;

    /**
     * @var EventInterface
     */
    protected $event;

    /**
     * @param ListenerInterface $listener
     * @return $this
     */
    public function setListener($listener)
    {
        $this->listener = $listener;
        return $this;
    }

    /**
     * @return ListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param \Skajdo\EventManager\Event\EventInterface $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return \Skajdo\EventManager\Event\EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }


}