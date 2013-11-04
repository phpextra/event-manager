<?php

namespace Skajdo\EventManager;
use Mockery\Mock;
use Skajdo\TestSuite\Test\TestFixture;

require_once(__DIR__ . '/../classes/TestClasses.php');

/**
 * Class BasicFunctionalityTest
 * @todo more isolated tests
 */
class BasicFunctionalityTest extends TestFixture
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

    public function testEventsAndListeners()
    {
        $event = new \DummyCancellableEvent2();

        $listener1 = new \DummyListener1();
        $listener2 = new \DummyListener2();

        $listener3 = function(EventInterface $event){
            if($event instanceof \DummyCancellableEvent){
                $event->events[] = 'Closure 1';
            }
        };

        $listener4 = function(\DummyCancellableEvent $event){
            $event->events[] = 'Closure 2';
        };

        $this->eventManager
            ->addListener($listener1)
            ->addListener($listener2)
            ->addListener($listener3, Priority::HIGH)
            ->addListener($listener4, Priority::LOWEST)
        ;

        $this->eventManager->trigger($event);
        $expectedLogs = array(
            'Closure 1',
            'Dummy 1',
            'Dummy 2 Event 1',
            'Dummy 2 Event 2',
            'Closure 2',
        );

        $this->assert()->isIdentical($expectedLogs, $event->events);
    }

    public function testInfiniteLoopDetection()
    {
        $this->eventManager->addListener(new \InfiniteLoopCauser($this->eventManager));
        $event = new \DummyCancellableEvent();

        try{
            $this->eventManager->trigger($event);
            throw new \Exception('Invalid');
        }catch (\Exception $e){
            $this->assert()->isInstanceOf('Exception', $e);
            $this->assert()->isIdentical('Recurrency', $e->getMessage());
        }

    }
}