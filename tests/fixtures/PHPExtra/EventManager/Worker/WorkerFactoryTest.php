<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Listener\AnonymousListener;
use PHPExtra\EventManager\Priority;

/**
 * The WorkerFactoryTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewInstance()
    {
        new WorkerFactory();
    }

    public function testCreateNewWorkerFromAnonymousListenerCreatesNewWorker()
    {
        $listener = new AnonymousListener(function(Event $event){}, Priority::HIGH);

        $factory = new WorkerFactory();
        $workers = $factory->createWorkers($listener);

        $this->assertTrue(is_array($workers));
        $this->assertEquals(1, count($workers));
        $this->assertEquals($workers[0]->getPriority(), Priority::HIGH);
    }

    public function testCreateNewWorkerFromStandardListener()
    {
        $listener = new \DummyListener1();

        $factory = new WorkerFactory();
        $workers = $factory->createWorkers($listener);

        $this->assertTrue(is_array($workers));
        $this->assertEquals(4, count($workers));
        $this->assertEquals($workers[0]->getPriority(), 100);
        $this->assertEquals($workers[1]->getPriority(), 500);
        $this->assertEquals($workers[2]->getPriority(), -1000);
        $this->assertEquals($workers[3]->getPriority(), -2000);
    }

    public function testFactoryDoesNotCreateWorkersFromPrivateNorProtectedMethods()
    {
        $listener = new \DummyListener3();

        $factory = new WorkerFactory();
        $workers = $factory->createWorkers($listener);

        $this->assertEquals(0, count($workers));
    }
}
 