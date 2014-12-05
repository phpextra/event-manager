<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\ListenerInterface;


/**
 * The WorkerInterface interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface WorkerInterface
{
    /**
     * Worker unique ID that will identify given worker during execution
     *
     * @return string
     */
    public function getId();

    /**
     * @param EventInterface $event
     *
     * @return WorkerResult
     */
    public function run(EventInterface $event);

    /**
     * Tell if current worker is listening to given event type
     *
     * @param EventInterface $event
     *
     * @return bool
     */
    public function isListeningTo(EventInterface $event);

    /**
     * Get listener attached to this worker
     *
     * @return ListenerInterface
     */
    public function getListener();

    /**
     * Get listener class name
     *
     * @return string
     */
    public function getListenerClass();

    /**
     * Get listener's method name
     *
     * @return string
     */
    public function getMethodName();

    /**
     * Get event class name
     *
     * @return string
     */
    public function getEventClass();

    /**
     * Get worker priority
     *
     * @return int
     */
    public function getPriority();
}