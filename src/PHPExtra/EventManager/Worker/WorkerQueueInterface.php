<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Worker;
use Psr\Log\LoggerAwareInterface;


/**
 * The WorkerQueueInterface interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface WorkerQueueInterface extends LoggerAwareInterface, \Countable, \IteratorAggregate
{
    /**
     * Add worker to the stack
     *
     * @param WorkerInterface $worker
     * @return $this
     */
    public function add(WorkerInterface $worker);

    /**
     * Is the queue empty?
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * @return WorkerInterface[]|\IteratorAggregate
     */
    public function getWorkers();
}