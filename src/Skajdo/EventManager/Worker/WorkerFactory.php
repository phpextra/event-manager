<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Worker;

use Skajdo\EventManager\Listener\ListenerMethod;

/**
 * The WorkerFactory class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerFactory
{
    /**
     * @deprecated use createWorker
     * @param \Skajdo\EventManager\Listener\ListenerMethod $method
     * @return Worker
     */
    public static function create(ListenerMethod $method)
    {
        return self::createWorker($method);
    }

    /**
     * @param ListenerMethod $job
     * @return Worker
     */
    public static function createWorker(ListenerMethod $job)
    {
        return new Worker($job->getListener(), $job->getMethodName(), $job->getEventClassName(), $job->getPriority());
    }
}