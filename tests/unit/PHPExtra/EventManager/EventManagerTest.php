<?php

declare(strict_types=1);

namespace PHPExtra\EventManager;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PHPExtra\EventManager\EventManager
 */
class EventManagerTest extends TestCase
{
    public function test_emit(): void
    {
        $em = new EventManager();
        $listener = new TestListener();

        $em->add($listener);
        $em->emit(new TestEvent());

        $this->assertSame(1, $listener->test1);
    }
}
