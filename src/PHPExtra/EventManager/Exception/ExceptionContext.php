<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager\Exception;

use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\ListenerInterface;

/**
 * The ExceptionContext interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class ExceptionContext
{
    /**
     * @var EventInterface
     */
    private $event;

    /**
     * @var ListenerInterface
     */
    private $listener;

    /**
     * @param EventInterface    $event
     * @param ListenerInterface $listener
     */
    function __construct(EventInterface $event, ListenerInterface $listener)
    {
        $this->event = $event;
        $this->listener = $listener;
    }

    /**
     * Tell if current context has an event
     *
     * @return bool
     */
    public function hasEvent()
    {
        return $this->event !== null;
    }

    /**
     * @return EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Tell if current context has a listener
     *
     * @return bool
     */
    public function hasListener()
    {
        return $this->listener !== null;
    }

    /**
     * @return ListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

} 