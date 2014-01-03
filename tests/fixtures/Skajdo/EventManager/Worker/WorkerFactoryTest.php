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
    public function testCreateNewInstance()
    {
        $factory = new WorkerFactory();
    }

    public function testCreateNewWorkerCreatesNewWorker()
    {
        $listener = new AnonymousListener(function(EventInterface $event){}, Priority::HIGH);
        $method = new ListenerMethod($listener, 'invoke', 'Skajdo\EventManager\Event\EventInterface', Priority::HIGH);

        $factory = new WorkerFactory();
        $worker = $factory->createWorker($method);

        $this->assertEquals($worker->getPriority(), Priority::HIGH);
    }
}
 