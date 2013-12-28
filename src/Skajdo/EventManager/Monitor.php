<?php

namespace Skajdo\EventManager;

use Skajdo\EventManager\Worker\Worker;
use Skajdo\EventManager\Worker\WorkerResult;

/**
 * The Monitor class
 * Monitor the work of event manager
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class Monitor
{

    public function eventLoopStarted(EventInterface $event){}

    public function eventLoopEnded(EventInterface $event){}

    public function workerAdded(Worker $worker){}

    public function workerStarted(Worker $worker, EventInterface $event){}

    public function workerEnded(Worker $worker, EventInterface $event, WorkerResult $result){}

    public function workerEndedWithoutListeners(Worker $worker){}

    public function doingRecurrencyCheck(Worker $worker){}

    public function recurrencyCheckFailed(Worker $worker){}

    public function changedExceptionFlag(Worker $worker){}


}