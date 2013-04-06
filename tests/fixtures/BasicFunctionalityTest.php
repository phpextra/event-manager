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

        $manager
            ->addListener($listener1)
            ->addListener($listener2)
            ->addListener(function(\DummyCancellableEvent $event){
                $event->sum = $event->sum + 11;
                $event->events[] = 'Anonymous 1' . ' + 11 (Closure)';
            }, Priority::HIGH)
            ->addListener(function(\DummyCancellableEvent $event){
                $event->sum = $event->sum - 6;
                $event->events[] = 'Anonymous 2' . ' - 6 (Closure)';
            }, Priority::LOWEST)
        ;
        $manager->triggerEvent($event);

        /**
         * DummyListener1 will be called first as it was added as first - FIFO order
         */

        $expectedEventFlow = array(
            'DummyListener2 * 5',
            'Anonymous 1 + 11 (Closure)',
            'DummyListener1 * 10',
            'DummyListener2 + 5',
            'Anonymous 2 - 6 (Closure)',
        );

        $this->assert()->isIdentical($expectedEventFlow, $event->events);
        $this->assert()->isIdentical(859, $event->sum);

    }
}