<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager\Worker;

/**
 * This is the default worker queue based on the \SplPriorityQueue
 * IMPORTANT: Iterating over a heap removes the values from the heap.
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class DefaultWorkerQueue extends \SplPriorityQueue implements WorkerQueueInterface
{
    /**
     * @var int
     */
    private $order = 0;

    /**
     * {@inheritdoc}
     */
    public function addWorker(WorkerInterface $worker)
    {
        $this->insert($worker, array($worker->getPriority(), $this->order++));
        return $this;
    }
}