<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Worker;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;


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

    /**
     * How many items are in the queue?
     *
     * @return int
     */
    public function count();

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger);
}