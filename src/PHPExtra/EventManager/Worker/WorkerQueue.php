<?php

/**
 * @copyright Jacek Kobus <kobus.jacek@gmail.com>
 */

namespace PHPExtra\EventManager\Worker;
use PHPExtra\EventManager\Event\Event;


/**
 * The Queue interface
 */
interface WorkerQueue extends \Countable
{
    /**
     * @param Worker $worker
     *
     * @return void
     */
    public function addWorker(Worker $worker);

    /**
     * Get workers that are able to handle the event
     *
     * @param Event $event
     *
     * @return \Iterator|Worker[]
     */
    public function getWorkersFor(Event $event);
}