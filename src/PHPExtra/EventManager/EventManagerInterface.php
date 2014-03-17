<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\ListenerInterface;

/**
 * The EventManagerInterface interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface EventManagerInterface extends LoggerAwareInterface
{
    /**
     * Return event that is currently running or null if no event is running
     *
     * @return EventInterface|null
     */
    public function getRunningEvent();

    /**
     * Tell if current instance of event manager will break the queue
     * if an exception will be thrown from listener.
     *
     * @return boolean
     */
    public function getThrowExceptions();

    /**
     * If this is set to true all exceptions will be thrown
     * and the queue will be interrupted (incomplete)
     *
     * Defaults to false
     *
     * @param bool $throwExceptions
     * @return EventManager
     */
    public function setThrowExceptions($throwExceptions);

    /**
     * Add event listener
     * Priority used in the listener can be overridden by setting the $priority
     *
     * @param ListenerInterface $listener
     * @param int               $priority
     * @return $this
     */
    public function addListener(ListenerInterface $listener, $priority = null);

    /**
     * Call all listeners that listen to given $event
     *
     * @param EventInterface $event
     * @throws \RuntimeException
     * @throws \Exception
     * @return $this
     */
    public function trigger(EventInterface $event);
}