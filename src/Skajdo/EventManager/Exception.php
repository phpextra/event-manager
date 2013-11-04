<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

use Skajdo\EventManager\Event;
use Skajdo\EventManager\Listener;

/**
 * Event manager exception
 *
 * @author      Jacek Kobus
 */
class Exception extends \Exception
{
    /**
     * @var Listener
     */
    protected $listener;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @param Listener $listener
     * @return $this
     */
    public function setListener($listener)
    {
        $this->listener = $listener;

        return $this;
    }

    /**
     * @return Listener
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param \Skajdo\EventManager\Event $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return \Skajdo\EventManager\Event
     */
    public function getEvent()
    {
        return $this->event;
    }


}