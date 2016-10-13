<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager;

use DummyCancellableEvent;
use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Exception\ListenerException;
use PHPExtra\EventManager\Listener\AnonymousListener;
use PHPExtra\EventManager\Listener\Listener;

/**
 * The EventManagerTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewInstance()
    {
        new EventManager();
    }

    public function testAddAnonymousListener()
    {
        $em = new EventManager();

        $listener = new AnonymousListener(function(Event $event){});
        $em->add($listener);
    }

    public function testExceptionThrowingIsDisabledByDefault()
    {
        $em = new EventManager();

        $event = new DummyCancellableEvent();
        $listener = new AnonymousListener(function(Event $event){
            throw new \Exception('test');
        });

        $em->add($listener)->emit($event);
    }

    public function testGetAllExceptionsCaughtDuringEmit()
    {
        $em = new EventManager();

        $event = new DummyCancellableEvent();

        $listener1 = new AnonymousListener(function(Event $event){
            throw new \Exception('test1');
        });

        $listener2 = new AnonymousListener(function(Event $event){
            throw new \Exception('test2');
        });

        $em->add($listener1);
        $em->add($listener2);
        $em->emit($event);

        // lifo order, last in first out
        $message2 = 'Listener "PHPExtra\EventManager\Listener\AnonymousListener::invoke()[P:0]" failed: "test1"';
        $message1 = 'Listener "PHPExtra\EventManager\Listener\AnonymousListener::invoke()[P:0]" failed: "test2"';

        $this->assertTrue($em->hasExceptions());
        $this->assertCount(2, $em->getExceptions());

        $this->assertInstanceOf(ListenerException::class, $em->getExceptions()[0]);
        $this->assertInstanceOf(Listener::class, $em->getExceptions()[0]->getListener());
        $this->assertInstanceOf(Event::class, $em->getExceptions()[0]->getEvent());
        $this->assertEquals($message1, $em->getExceptions()[0]->getMessage());

        $this->assertInstanceOf(ListenerException::class, $em->getExceptions()[1]);
        $this->assertInstanceOf(Listener::class, $em->getExceptions()[1]->getListener());
        $this->assertInstanceOf(Event::class, $em->getExceptions()[1]->getEvent());
        $this->assertEquals($message2, $em->getExceptions()[1]->getMessage());
    }

    /**
     * @expectedException \PHPExtra\EventManager\Exception\ListenerException
     */
    public function testExceptionThrownDuringWorkerExecutionIsProperlyHandledAndRethrown()
    {
        $em = new EventManager();
        $em->setThrowExceptions(true);

        $event = new DummyCancellableEvent();
        $listener = new AnonymousListener(function(Event $event){
            throw new \Exception('test');
        });

        $em->add($listener)->emit($event);
    }

    public function testNotifiesAnonymousListener()
    {
        $event = $this->createMock('PHPExtra\EventManager\Event\Event');
        $em = new EventManager();

        $wasRun = false;

        $listener = new AnonymousListener(function(Event $event) use (&$wasRun){
            $wasRun = true;
        });

        $em->add($listener);
        $em->emit($event);

        $this->assertTrue($wasRun, 'Listener was NOT notified');

    }

    public function testEmittedEventRunsThroughAnonymousListenersInProperOrder()
    {
        $event = new \DummyEvent();
        $em = new EventManager();

        $calls = array();

        $listeners[] = new AnonymousListener(function(Event $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'A';
            }
        });

        $listeners[] = new AnonymousListener(function(Event $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'B';
            }
        });

        $listeners[] = new AnonymousListener(function(Event $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'C';
            }
        });

        $listeners[] = new AnonymousListener(function(Event $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'D';
            }
        });

        $listeners[] = new AnonymousListener(function(Event $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'E';
            }
        });

        $listeners[] = new AnonymousListener(function(Event $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'F';
            }
        });

        $listeners[] = new AnonymousListener(function(Event $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'G';
            }
        });

        $listeners[] = new AnonymousListener(function(Event $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'H';
            }
        });

        $em->add($listeners[3], Priority::HIGHEST);     // D
        $em->add($listeners[4], Priority::HIGHER);      // E
        $em->add($listeners[1], Priority::HIGH);        // B
        $em->add($listeners[7], Priority::NORMAL);      // H
        $em->add($listeners[2], Priority::LOW);         // C
        $em->add($listeners[5], Priority::LOWER);       // F
        $em->add($listeners[6], Priority::LOWEST);      // G
        $em->add($listeners[0], Priority::MONITOR);     // A

        $em->emit($event);

        $expected = array('D', 'E', 'B', 'H', 'C', 'F', 'G', 'A');

        $this->assertEquals($expected, $event->calls);

    }

    public function testEmittedEventRunsThroughStandardListenersInProperOrder()
    {
        $em = new EventManager();

        $event = new DummyCancellableEvent();
        $listener = new \DummyListener1();

        $em->add($listener)->emit($event);

        $expected = array(
            'Dummy 1.1',
            'Dummy 1.2',
            'Dummy 1.3',
            'Dummy 1.4'
        );

        $this->assertEquals($expected, $event->events);
    }
}
 