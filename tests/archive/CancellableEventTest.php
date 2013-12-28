<?php

namespace Skajdo\EventManager;

class CancellableEventTest extends \PHPUnit_Framework_TestCase
{
    public function testCancellableEventCanBeCancelled()
    {
        $event = new CancellableEvent();
        $event->setIsCancelled();
        $this->assertTrue($event->isCancelled());
    }
}