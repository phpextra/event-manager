<?php

namespace Skajdo\EventManager;
use Mockery\Mock;
use Skajdo\TestSuite\Test\TestFixture;

require_once(__DIR__ . '/../classes/TestClasses.php');

class PerformanceTest extends TestFixture
{
    public function testPerformanceTest()
    {
        $this->markAsSkipped('Benchmark test skipped !');
    }
}