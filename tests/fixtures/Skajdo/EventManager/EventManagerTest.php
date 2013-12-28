<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

use Skajdo\EventManager\Listener\AnonymousListener;

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

    public function testAddListenersAddsListeners()
    {
        $em = new EventManager();
        $listener1 = new AnonymousListener(function(){});
        $listener2 = new AnonymousListener(function(){});

        $this->assertEquals(0, $em->getWorkerQueue()->count());
        $em->addListener($listener1)->addListener($listener2);
        $this->assertEquals(2, $em->getWorkerQueue()->count());
    }




}
 