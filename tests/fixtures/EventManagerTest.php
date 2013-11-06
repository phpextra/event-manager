<?php

namespace Skajdo\EventManager;
use Mockery\Mock;
use Skajdo\EventManager\Listener\AnonymousListener;
use Skajdo\TestSuite\Test\TestFixture;

require_once(__DIR__ . '/../classes/TestClasses.php');

/**
 * Class EventManagerTest
 * @package Skajdo\EventManager
 */
class EventManagerTest extends TestFixture
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

    public function testEventManagerAcceptsAnonymousListeners()
    {
        $this->eventManager->addListener(new AnonymousListener(function(){

        }));
    }
}