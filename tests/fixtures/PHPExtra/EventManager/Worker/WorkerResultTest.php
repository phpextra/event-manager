<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager\Worker;

/**
 * The WorkerResultTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerResultTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewInstance()
    {
        $event = new \DummyEvent();
        $worker = new Worker(1, new \DummyListener1(), 'asd', 1);
        new WorkerResult($worker, $event);
    }
}
 