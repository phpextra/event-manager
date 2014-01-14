<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

use Skajdo\EventManager\Event\EventInterface;
use Skajdo\EventManager\Listener\AnonymousListener;

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

    public function testAddListenersAddsListeners()
    {
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

    public function testExecuteAnonymousListenersInProperOrderReturnsValidResult()
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

        $em->addListener($listeners[0], Priority::MONITOR);
        $em->addListener($listeners[1], Priority::HIGH);
        $em->addListener($listeners[2], Priority::LOW);
        $em->addListener($listeners[3], Priority::HIGHEST);

        $em->trigger($event);

        $expected = array('D', 'B', 'C', 'A');

        $this->assertEquals($expected, $event->calls);

    }
}
 