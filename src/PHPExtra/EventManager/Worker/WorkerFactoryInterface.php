<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Listener\ListenerInterface;

/**
 * The WorkerFactoryInterface interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface WorkerFactoryInterface
{
    /**
     * Create workers from given listener
     *
     * @param ListenerInterface $listener
     *
     * @return WorkerInterface[]
     */
    public function createWorkers(ListenerInterface $listener);
}