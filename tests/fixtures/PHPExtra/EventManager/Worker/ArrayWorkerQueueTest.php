<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Worker;
use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Listener\Listener;
use PHPExtra\EventManager\Priority;

class ArrayWorkerQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WorkerQueue
     */
    protected $queue;

    protected function setUp()
    {
        $this->queue = new ArrayWorkerQueue();
    }

    private function workers()
    {
        $listener = $this->createMock(Listener::class);

        return array(
            new Worker(1, $listener, 'dummy1', Event::class, Priority::NORMAL),
            new Worker(2, $listener, 'dummy2', Event::class, Priority::NORMAL),
            new Worker(3, $listener, 'dummy3', Event::class, Priority::NORMAL),
        );
    }

    public function testCreateNewInstance()
    {
        $this->assertEquals(0, count($this->queue));
    }

    public function testAddWorkersToTheQueue()
    {
        $workers = $this->workers();

        $this->queue->addWorker($workers[0]);
        $this->assertEquals(1, count($this->queue));

        $this->queue->addWorker($workers[1]);
        $this->assertEquals(2, count($this->queue));

        $this->queue->addWorker($workers[2]);
        $this->assertEquals(3, count($this->queue));
    }

    public function testReturnWorkersInLifoOrder()
    {
        $workers = $this->workers();

        /** @var Event $event */
        $event = $this->createMock(Event::class);

        $this->queue->addWorker($workers[0]);
        $this->queue->addWorker($workers[1]);
        $this->queue->addWorker($workers[2]);

        $output = array();

        foreach($this->queue->getWorkersFor($event) as $worker){
            /** @var Worker $worker */
            $output[] = $worker->getMethodName();
        }

        $this->assertEquals(array('dummy3','dummy2','dummy1'), $output);
    }
}
