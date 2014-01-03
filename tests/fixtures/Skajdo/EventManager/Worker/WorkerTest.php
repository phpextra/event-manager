<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace Skajdo\EventManager\Worker;

use Skajdo\EventManager\Event\EventInterface;
use Skajdo\EventManager\Listener\AnonymousListener;
use Skajdo\EventManager\Priority;

/**
 * The WorkerTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewInstance()
    {
        $listener = new AnonymousListener(function(EventInterface $event){}, Priority::HIGH);
        $worker = new Worker($listener, 'invoke', 'Skajdo\EventManager\Event\EventInterface');
    }
}
 