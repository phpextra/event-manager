<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Worker;


/**
 * The WorkerQueueInterface interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface WorkerQueueInterface extends \Countable, \Iterator
{
    /**
     * Add worker to the stack
     *
     * @param WorkerInterface $worker
     *
     * @return $this
     */
    public function addWorker(WorkerInterface $worker);

    /**
     * Is the queue empty?
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * @return array|WorkerInterface[]
     */
    public function getWorkers();
}