<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager\Event;

/**
 * The CancellableEventTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class CancellableEventTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewInstance()
    {
        $event = $this->getMockForAbstractClass('PHPExtra\EventManager\Event\CancellableEvent');
        /** @var CancellableEvent $event */

        $this->assertFalse($event->isCancelled());
    }

    public function testCancelCancellableEvent()
    {
        $event = $this->getMockForAbstractClass('PHPExtra\EventManager\Event\CancellableEvent');
        /** @var CancellableEvent $event */

        $event->cancel();
        $this->assertTrue($event->isCancelled());
    }

    public function testCancelCancellableEventWithReason()
    {
        $event = $this->getMockForAbstractClass('PHPExtra\EventManager\Event\CancellableEvent');
        /** @var CancellableEvent $event */

        $event->cancel('Some reason');
        $this->assertTrue($event->isCancelled());
        $this->assertEquals('Some reason', $event->getReason());
    }
}
