<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Worker;

use Skajdo\EventManager\Listener\ListenerMethod;

/**
 * Creates workers
 */
class WorkerFactory
{
    /**
     * @param \Skajdo\EventManager\Listener\ListenerMethod $method
     * @return Worker
     */
    public static function create(ListenerMethod $method)
    {
        return new Worker($method->getListener(), $method->getMethodName(), $method->getEventClassName(), $method->getPriority());
    }
}