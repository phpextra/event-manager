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
 * The WorkerTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewInstance()
    {
        $listener = new AnonymousListener(function(Event $event){}, Priority::HIGH);
        $worker = new Worker(1, $listener, 'invoke', 'PHPExtra\EventManager\Event\EventInterface');
    }
}
 