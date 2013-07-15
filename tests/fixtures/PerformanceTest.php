<?php

namespace Skajdo\EventManager;
use DummyCancellableEvent;
use Skajdo\TestSuite\Fixture;

require_once(__DIR__ . '/../classes/TestClasses.php');

class PerformanceTest extends Fixture
{
    public function benchmarkTest()
    {

        $logs = array();

        $logger = $this->mock('\Psr\Log\NullLogger');
        $logger->shouldDeferMissing();
        $logger->shouldReceive('log')->andReturnUsing(function($level, $message) use (&$logs){
            $logs[] = $message;
        });

        $em = new EventManager($logger);
        $event = new DummyCancellableEvent();

        for($i=0;$i<100;$i++){
            $em->addListener(function(DummyCancellableEvent $event){
                // do nothing
            });
        }

        $start = microtime(true);

        for($ii=0;$ii<50;$ii++){
            $em->triggerEvent($event);
        }

        $stop = bcsub(microtime(true), $start, 6);

        // 10150 calls must run under 0.8
        $this->assert()->isIdentical(-1, bccomp($stop, 0.8, 6));

    }
}