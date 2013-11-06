<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Listener;

/**
 * Due to the nature of listeners a proxy object is required to create a
 * simplified interface for each kind of listener.
 */
class ListenerProxy implements NormalizedListenerInterface
{
    /**
     * @var NormalizedListenerInterface
     */
    protected $listener;

    /**
     * @param ListenerInterface $listener
     */
    function __construct(ListenerInterface $listener)
    {
        if(!$listener instanceof NormalizedListenerInterface){
            // Listener must a class that should be reflected in order to obtain information about events
            $listener = new ReflectedListener($listener);
        }
        $this->listener = $listener;
    }

    /**
     * @return \Skajdo\EventManager\Listener\NormalizedListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * Return event class name paired with method that should be called for that event
     *
     * @return ListenerMethod[]
     */
    public function getListenerMethods()
    {
        return $this->listener->getListenerMethods();
    }
}