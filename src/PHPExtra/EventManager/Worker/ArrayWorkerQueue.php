<?php

/**
 * @copyright Jacek Kobus <kobus.jacek@gmail.com>
 */

namespace PHPExtra\EventManager\Worker;
use PHPExtra\EventManager\Event\Event;

/**
 * The ArrayWorkerQueue class
 */
class ArrayWorkerQueue implements WorkerQueue
{
    /**
     * @var array
     */
    private $eventToWorkerMap = array();

    /**
     * Used to maintain LIFO order
     *
     * @var int
     */
    private $order = 0;

    /**
     * Store amount of items in queue
     *
     * @var int
     */
    private $count = 0;

    /**
     * @param Worker $worker
     *
     * @return void
     */
    public function addWorker(Worker $worker)
    {
        $this->eventToWorkerMap[$worker->getEventClass()][$this->order++] = $worker;
        $this->count++;
    }

    /**
     * Get workers that are able to handle the Event
     *
     * @param Event $event
     *
     * @return Worker[]
     */
    public function getWorkersFor(Event $event)
    {
        $queue = new \SplPriorityQueue();
        foreach($this->eventToWorkerMap as $eventClass => $workers){
            if($event instanceof $eventClass){
                /** @var Worker[] $workers */
                foreach($workers as $lifoIndex => $worker){
                    $queue->insert($worker, array($worker->getPriority(), $lifoIndex));
                }
            }
        }
        return $queue;
    }

    public function count()
    {
        return $this->count;
    }
}