<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Worker;
use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Listener\Listener;
use PHPExtra\EventManager\Priority;

/**
 * The SortableWorkerQueueTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
abstract class WorkerQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WorkerQueue
     */
    protected $queue;

    public static function workers()
    {
        $mockGenerator = new \PHPUnit_Framework_MockObject_Generator();
        $event = 'PHPExtra\EventManager\Event\Event';
        $listener = $mockGenerator->getMock('PHPExtra\EventManager\Listener\Listener');

        /** @var Listener $listener */

        return array(
            array(
                array(
                    new Worker(1, $listener, 'dummy1', $event, Priority::NORMAL),
                    new Worker(2, $listener, 'dummy2', $event, Priority::NORMAL),
                    new Worker(3, $listener, 'dummy3', $event, Priority::NORMAL),
                )
            )
        );
    }

    public function testCreateNewInstance()
    {
        $this->assertEquals(0, count($this->queue));
    }

    /**
     * @dataProvider PHPExtra\EventManager\Worker\WorkerQueueTest::workers
     *
     * @param Worker[]|array $workers
     */
    public function testAddWorkersToTheQueue(array $workers)
    {
        $this->queue->addWorker($workers[0]);
        $this->assertEquals(1, count($this->queue));

        $this->queue->addWorker($workers[1]);
        $this->assertEquals(2, count($this->queue));

        $this->queue->addWorker($workers[2]);
        $this->assertEquals(3, count($this->queue));
    }

    /**
     * @dataProvider PHPExtra\EventManager\Worker\WorkerQueueTest::workers
     *
     * @param Worker[]|array $workers
     */
    public function testReturnWorkersInLifoOrder(array $workers)
    {
        $event = $this->getMock('PHPExtra\EventManager\Event\Event');

        /** @var Event $event */

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
