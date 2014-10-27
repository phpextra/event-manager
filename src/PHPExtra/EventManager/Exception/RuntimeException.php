<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Exception;

use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\ListenerInterface;
use RuntimeException as SplRuntimeException;

/**
 * The RuntimeException class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class RuntimeException extends SplRuntimeException
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
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        SplRuntimeException::__construct($message, $code, $previous);
    }

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