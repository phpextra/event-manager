<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Listener;

/**
 * Proxy-like object is required to create a simplified interface for each kind of listener
 *
 * @deprecated
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WrappedListener implements NormalizedListenerInterface
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
     * @return NormalizedListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerMethods()
    {
        return $this->getListener()->getListenerMethods();
    }
}