<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager;
use Skajdo\EventManager\Listener\ListenerInterface;

/**
 * The Exception class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
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
     * @param EventInterface $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }


}