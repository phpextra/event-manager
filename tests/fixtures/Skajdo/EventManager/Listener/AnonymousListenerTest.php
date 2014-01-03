<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace Skajdo\EventManager\Listener;

use Skajdo\EventManager\Event\EventInterface;
use Skajdo\EventManager\Priority;

/**
 * The AnonymousListenerTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class AnonymousListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewListenerInstance()
    {
        $listener = new AnonymousListener(function(EventInterface $event){}, Priority::HIGH);
    }

    public function testGetListenersPriorityReturnsCorrectPriorityValue()
    {
        $listener = new AnonymousListener(function(EventInterface $event){}, Priority::HIGH);
        $this->assertEquals(Priority::HIGH, $listener->getPriority());
    }

    public function testGetListenersClosureReturnsCorrectObject()
    {
        $closure = function(EventInterface $event){};
        $listener = new AnonymousListener($closure, Priority::HIGH);
        $this->assertEquals($closure, $listener->getClosure());
    }

    public function testGetListenersMethodsReturnsSingleMethod()
    {
        $closure = function(EventInterface $event){};
        $listener = new AnonymousListener($closure, Priority::HIGH);

        $methods = $listener->getListenerMethods();
    }
}
 