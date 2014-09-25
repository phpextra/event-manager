<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use Zend\Stdlib\PriorityQueue;

/**
 * The WorkerQueue class
 * Uses Zend's PriorityQueue
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerQueue extends AbstractWorkerQueue
{
    /**
     * @var PriorityQueue
     */
    protected $queue;

    function __construct()
    {
        $this->queue = new PriorityQueue();
    }

    /**
     * {@inheritdoc}
     */
    public function add(WorkerInterface $worker)
    {
        $this->queue->insert($worker, $worker->getPriority());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkers()
    {
        return $this->queue;
    }
}