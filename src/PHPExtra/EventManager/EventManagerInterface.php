<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\ListenerInterface;

/**
 * The EventManagerInterface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface EventManagerInterface
{
    /**
     * Add event listener
     * Priority used in the listener can be overridden by setting the $priority
     *
     * @see Priority
     *
     * @param ListenerInterface $listener
     * @param int               $priority
     *
     * @return $this
     */
    public function addListener(ListenerInterface $listener, $priority = null);

    /**
     * Call all listeners that listen to given $event
     *
     * @param EventInterface $event
     *
     * @throws \RuntimeException
     * @return $this
     */
    public function trigger(EventInterface $event);
}