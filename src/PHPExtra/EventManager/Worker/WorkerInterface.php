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
     * @return ListenerInterface
     */
    public function getListener();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getEventClass();

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority);

    /**
     * @return int
     */
    public function getPriority();
}