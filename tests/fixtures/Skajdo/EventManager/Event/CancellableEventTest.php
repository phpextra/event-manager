<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace Skajdo\EventManager\Event;

/**
 * The CancellableEventTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class CancellableEventTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCancellableEvent()
    {
        $event = new CancellableEvent();
        $this->assertFalse($event->isCancelled());
    }

    public function testCancelCancellableEventMakesEventCancelled()
    {
        $event = new CancellableEvent();
        $this->assertFalse($event->isCancelled());

        $event->setIsCancelled();

        $this->assertTrue($event->isCancelled());
    }
}
 