<?php

namespace Skajdo\EventManager;
use Skajdo\TestSuite\Test\TestFixture;

class QueueTest extends TestFixture
{
    public function setUp(){}
    public function tearDown(){}

    public function testQueueOrder()
    {
        $queue = new Queue();

        $queue->insert('e', -100);
        $queue->insert('a', 20); // 3 FIFO - first in first out
        $queue->insert('b', 20); // 4
        $queue->insert('c', 30); // 2
        $queue->insert('d', 40); // 1

        $this->assert()->isIdentical(5, $queue->count());

        $internalQueue = $queue->getIterator();

        $this->assert()->isIdentical('d', $internalQueue->top());
        $internalQueue->extract();

        $this->assert()->isIdentical('c', $internalQueue->top());
        $internalQueue->extract();

        $this->assert()->isIdentical(3, $internalQueue->count());

        $this->assert()->isIdentical('a', $internalQueue->top());
        $internalQueue->extract();

        $this->assert()->isIdentical('b', $internalQueue->top());
        $internalQueue->extract();

        $this->assert()->isIdentical('e', $internalQueue->top());

        $this->assert()->isIdentical($internalQueue->toArray(), array(0 => 'e'));
    }
}