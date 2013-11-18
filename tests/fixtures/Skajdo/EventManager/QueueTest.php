<?php

namespace Skajdo\EventManager;

use Skajdo\EventManager\Worker\WorkerQueue;

class QueueTest extends \PHPUnit_Framework_TestCase
{
    public function testQueueReturnsItemsInCorrectOrder()
    {
        $queue = new WorkerQueue();

        $queue->insert('e', -100);
        $queue->insert('a', 20); // 3 FIFO - first in first out
        $queue->insert('b', 20); // 4
        $queue->insert('c', 30); // 2
        $queue->insert('d', 40); // 1

        $this->assertEquals(5, $queue->count());

        $internalQueue = $queue->getIterator();

        $this->assertEquals('d', $internalQueue->top());
        $internalQueue->extract();

        $this->assertEquals('c', $internalQueue->top());
        $internalQueue->extract();

        $this->assertEquals(3, $internalQueue->count());

        $this->assertEquals('a', $internalQueue->top());
        $internalQueue->extract();

        $this->assertEquals('b', $internalQueue->top());
        $internalQueue->extract();

        $this->assertEquals('e', $internalQueue->top());

        $this->assertEquals($internalQueue->toArray(), array(0 => 'e'));
    }
}