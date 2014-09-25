<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Exception;

use Exception as SplException;
use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\ListenerInterface;

/**
 * The Exception class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class Exception extends SplException
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
     * @return ListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param ListenerInterface $listener
     *
     * @return $this
     */
    public function setListener($listener)
    {
        $this->listener = $listener;

        return $this;
    }

    /**
     * @return EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param EventInterface $event
     *
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }


}