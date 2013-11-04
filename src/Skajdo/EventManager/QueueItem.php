<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

use Skajdo\EventManager\Listener;

/**
 * Queue item.
 *
 * @author Jacek Kobus
 */
class QueueItem
{
    /**
     * @var ListenerInterface|\Closure
     */
    protected $listener;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $eventClass;

    /**
     * @var int
     */
    protected $priority;

    /**
     * Create new queue item
     *
     * @param ListenerInterface|\Closure $listener
     * @param string $method
     * @param string $eventClass
     * @param int $priority
     * @throws \InvalidArgumentException If Listener is not an instance of Listener interface nor Closure
     */
    public function __construct($listener, $method, $eventClass, $priority = Priority::NORMAL)
    {
        $this->listener = $listener;
        $this->method = $method;
        $this->eventClass = $eventClass;
        $this->priority = $priority;
    }

    /**
     * @param string $eventClass
     */
    public function setEventClass($eventClass)
    {
        $this->eventClass = $eventClass;
    }

    /**
     * @return string
     */
    public function getEventClass()
    {
        return $this->eventClass;
    }

    /**
     * @param \Skajdo\EventManager\ListenerInterface $listener
     */
    public function setListener($listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return \Skajdo\EventManager\ListenerInterface|\Closure
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

}