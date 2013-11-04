<?php

namespace Skajdo\EventManager;
use Skajdo\TestSuite\Test\TestFixture;

class CancellableEventTest extends TestFixture
{
    public function testEventCanBeCancelled()
    {
        $event = new CancellableEvent();
        $event->setIsCancelled();
        $this->assert()->isTrue($event->isCancelled());
    }
}