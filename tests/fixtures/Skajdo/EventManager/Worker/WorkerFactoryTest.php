<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace Skajdo\EventManager\Worker;

use Skajdo\EventManager\Event\EventInterface;
use Skajdo\EventManager\Listener\AnonymousListener;
use Skajdo\EventManager\Listener\ListenerMethod;
use Skajdo\EventManager\Priority;

/**
 * The WorkerFactoryTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewWorkerFactoryInstance()
    {
        $factory = new WorkerFactory();
    }

    public function testCreateNewWorkerCreatesNewWorker()
    {
        $this->markTestIncomplete();
        $listener = new AnonymousListener(function(EventInterface $event){}, Priority::HIGH);

        $factory = new WorkerFactory();
        $workers = $factory->createWorkers($listener);

        $this->assertTrue(is_array($workers));
        $this->assertEquals(1, count($workers));
        $this->assertEquals($workers[0]->getPriority(), Priority::HIGH);
    }
}
 