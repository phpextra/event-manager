<?php

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Listener\AnonymousListener;

require_once(__DIR__ . '/../classes/TestClasses.php');

class BasicFunctionalityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * Array of log messages
     *
     * @var array
     */
    protected $logs = array();

    public function setUp()
    {
        $this->eventManager = new EventManager();
        $this->eventManager->setLogger($this->getLogger());
    }

    public function tearDown()
    {
        $this->logs = array();
        $this->eventManager = null;
        \Mockery::close();
    }

    /**
     * @return \Mockery\MockInterface|\Psr\Log\NullLogger
     */
    protected function getLogger()
    {
        $logs =& $this->logs;
        $logger = \Mockery::mock('\Psr\Log\NullLogger');
        $logger->shouldDeferMissing();
        $logger->shouldReceive('log')->andReturnUsing(function($level, $message) use (&$logs){
            $logs[] = $message;
        });
        return $logger;
    }

    public function testEventManagerDoesNothingIfNoListenersGiven()
    {
        $this->eventManager->trigger($event = new \DummyCancellableEvent2());
        $this->assertEquals(array(), $event->events);
    }

    public function testEventManagerTriggersEventsAndListenersInProperOrder()
    {
        $event = new \DummyCancellableEvent2();

        $listener1 = new \DummyListener1();
        $listener2 = new \DummyListener2();

        $listener3 = new AnonymousListener(function(EventInterface $event){
            if($event instanceof \DummyCancellableEvent){
                $event->events[] = 'Closure 1';
            }
        });

        $listener4 = new AnonymousListener(function(\DummyCancellableEvent $event){
            $event->events[] = 'Closure 2';
        });

        $this->eventManager
            ->addListener($listener1)
            ->addListener($listener2)
            ->addListener($listener3, Priority::HIGHEST)
            ->addListener($listener4, Priority::LOWEST)
        ;

        $this->eventManager->trigger($event);
        $expectedLogs = array(
            'Closure 1',
            'Dummy 2 Event 2',
            'Dummy 1',
            'Dummy 2 Event 1',
            'Closure 2',
        );

        $this->assertEquals($expectedLogs, $event->events);
    }

    public function testEventManagerDetectsRecurrencyInListeners()
    {
        $this->eventManager
            ->setThrowExceptions(true)
            ->addListener(new \InfiniteLoopCauser($this->eventManager));
        $event = new \DummyCancellableEvent();

        try{
            $this->eventManager->trigger($event);
            throw new \Exception('Event manager was not able to detect recurrency');
        }catch (\Exception $e){
            $this->assertInstanceOf('Exception', $e);
            $this->assertEquals('Recurrency', $e->getMessage());
        }

    }
}