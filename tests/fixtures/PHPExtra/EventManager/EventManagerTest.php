<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager;

use DummyCancellableEvent;
use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\AnonymousListener;

/**
 * The EventManagerTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateEventManagerCreatesEventManager()
    {
        new EventManager();
    }

    public function testAddAnonymousListenersAddsListeners()
    {
        $this->markTestIncomplete();
        $em = new EventManager();
        $listener1 = new AnonymousListener(function(EventInterface $event){});
        $listener2 = new AnonymousListener(function(EventInterface $event){});

        $this->assertEquals(0, $em->getWorkerQueue()->count());

        $em
            ->addListener($listener1)
            ->addListener($listener2)
        ;

        $this->assertEquals(2, $em->getWorkerQueue()->count());
    }

    public function testExecuteListenersInProperOrderReturnsValidResult()
    {
        $event = new \DummyEvent();
        $em = new EventManager();

        $calls = array();

        $listeners[] = new AnonymousListener(function(EventInterface $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'A';
            }
        });

        $listeners[] = new AnonymousListener(function(EventInterface $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'B';
            }
        });

        $listeners[] = new AnonymousListener(function(EventInterface $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'C';
            }
        });

        $listeners[] = new AnonymousListener(function(EventInterface $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'D';
            }
        });

        $listeners[] = new AnonymousListener(function(EventInterface $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'E';
            }
        });

        $listeners[] = new AnonymousListener(function(EventInterface $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'F';
            }
        });

        $listeners[] = new AnonymousListener(function(EventInterface $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'G';
            }
        });

        $listeners[] = new AnonymousListener(function(EventInterface $event) use (&$calls){
            if($event instanceof \DummyEvent){
                $event->calls[] = 'H';
            }
        });

        $em->addListener($listeners[3], Priority::HIGHEST);     // D
        $em->addListener($listeners[4], Priority::HIGHER);      // E
        $em->addListener($listeners[1], Priority::HIGH);        // B
        $em->addListener($listeners[7], Priority::NORMAL);      // H
        $em->addListener($listeners[2], Priority::LOW);         // C
        $em->addListener($listeners[5], Priority::LOWER);       // F
        $em->addListener($listeners[6], Priority::LOWEST);      // G
        $em->addListener($listeners[0], Priority::MONITOR);     // A

        $em->trigger($event);

        $expected = array('D', 'E', 'B', 'H', 'C', 'F', 'G', 'A');

        $this->assertEquals($expected, $event->calls);

    }

    public function testTriggerStandardListenersExecutesListenersInCorrectOrder()
    {
        $em = new EventManager();

        $event = new DummyCancellableEvent();
        $listener = new \DummyListener1();

        $em->addListener($listener)->trigger($event);

        $expected = array(
            'Dummy 1.1',
            'Dummy 1.2',
            'Dummy 1.3',
            'Dummy 1.4'
        );

        $this->assertEquals($expected, $event->events);
    }

    public function testExceptionThrowingIsDisabledByDefault()
    {
        $em = new EventManager();

        $event = new DummyCancellableEvent();
        $listener = new AnonymousListener(function(EventInterface $event){
                throw new \Exception('test');
            });

        $em->addListener($listener)->trigger($event);
    }

    /**
     * @expectedException \PHPExtra\EventManager\Exception\RuntimeException
     */
    public function testExceptionThrownDuringWorkerExecutionIsProperlyHandledAndRethrown()
    {
        $em = new EventManager();
        $em->setThrowExceptions(true);

        $event = new DummyCancellableEvent();
        $listener = new AnonymousListener(function(EventInterface $event){
            throw new \Exception('test');
        });

        $em->addListener($listener)->trigger($event);
    }
}
 