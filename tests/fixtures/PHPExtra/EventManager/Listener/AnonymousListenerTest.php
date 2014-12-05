<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager\Listener;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Priority;

/**
 * The AnonymousListenerTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class AnonymousListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewInstance()
    {
        new AnonymousListener(function(Event $event){}, Priority::HIGH);
    }

    public function testListenerReturnsValidPriority()
    {
        $listener = new AnonymousListener(function(Event $event){}, Priority::HIGH);
        $this->assertEquals(Priority::HIGH, $listener->getPriority());
    }

    public function testListenerReturnsClosure()
    {
        $closure = function(Event $event){};
        $listener = new AnonymousListener($closure, Priority::HIGH);
        $this->assertEquals($closure, $listener->getClosure());
    }
}
 