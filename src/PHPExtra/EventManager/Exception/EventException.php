<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Exception;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Listener\Listener;

/**
 * The EventException class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventException extends \RuntimeException
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var Listener
     */
    private $listener;

    /**
     * @param Event      $event
     * @param Listener   $listener
     * @param string     $message
     * @param \Exception $previous
     */
    public function __construct(Event $event, Listener $listener, $message, \Exception $previous = null)
    {
        $this->event = $event;
        $this->listener = $listener;

        parent::__construct($message, 1, $previous);
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return Listener
     */
    public function getListener()
    {
        return $this->listener;
    }
}