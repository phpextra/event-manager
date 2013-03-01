<?php

namespace Skajdo\EventManager;
use Skajdo\TestSuite\Fixture;

require_once(__DIR__ . '/../classes/TestClasses.php');

class BasicFunctionalityTest extends Fixture
{
    public function testEventsAndListeners()
    {
        $manager = new EventManager();
        $event = new \DummyCancellableEvent();
        $listener1 = new \DummyListener1();
        $listener2 = new \DummyListener2();

        $manager->addListener($listener1)->addListener($listener2);
        $manager->triggerEvent($event);

        $this->assert()->isIdentical($event->sum, 155);

    }
}