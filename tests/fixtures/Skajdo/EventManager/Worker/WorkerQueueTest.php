<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

/**
 * The WorkerQueueTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerQueueTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateEmptyQueueCreatesEmptyQueue()
    {
        $queue = new WorkerQueue();
        $this->assertTrue($queue->isEmpty());
        $this->assertEquals(0, $queue->count());
        $this->assertEquals(0, count($queue));
    }

    public function testAddWorkerAddsWorkerToTheQueue()
    {
        $queue = new WorkerQueue();
        $listener = $this->getMock('PHPExtra\EventManager\Listener\ListenerInterface');
        $worker1 = new Worker($listener, 'dummy', 'dummy');
        $worker2 = new Worker($listener, 'dummy', 'dummy');
        $worker3 = new Worker($listener, 'dummy', 'dummy');

        $queue->add($worker1);

        $this->assertFalse($queue->isEmpty());
        $this->assertEquals(1, $queue->count());
        $this->assertEquals(1, count($queue));

        $queue->add($worker2);

        $this->assertFalse($queue->isEmpty());
        $this->assertEquals(2, $queue->count());
        $this->assertEquals(2, count($queue));

        $queue->add($worker3);

        $this->assertFalse($queue->isEmpty());
        $this->assertEquals(3, $queue->count());
        $this->assertEquals(3, count($queue));

    }


}
 