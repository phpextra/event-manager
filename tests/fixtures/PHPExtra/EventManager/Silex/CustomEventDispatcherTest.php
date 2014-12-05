<?php

namespace fixtures;

use PHPExtra\EventManager\EventManager;
use PHPExtra\EventManager\Listener\AnonymousListener;
use PHPExtra\EventManager\Silex\CustomEventDispatcher;
use PHPExtra\EventManager\Silex\SilexEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * The CustomEventDispatcherTest class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class CustomEventDispatcherTest extends \PHPUnit_Framework_TestCase 
{

    public function testCreateNewInstance()
    {
        new CustomEventDispatcher();
    }

    public function testDispatcherTriggersEventManager()
    {
        $expected = array('test event');
        $events = array();

        $em = new EventManager();
        $em->add(new AnonymousListener(function(SilexEvent $event) use (&$events){
            $events[] = $event->getName();
        }));

        $dispatcher = new CustomEventDispatcher();
        $dispatcher->setEventManager($em);

        $dispatcher->dispatch('test event', new Event());

        $this->assertEquals($expected, $events);
    }


}
