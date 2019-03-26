<?php

declare(strict_types=1);

namespace PHPExtra\EventManager;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PHPExtra\EventManager\Notifier
 */
class NotifierTest extends TestCase
{
    /**
     * @covers ::notify
     * @covers ::supports
     * @covers ::call
     */
    public function test_notify(): void
    {
        $notifier = new Notifier();
        $listener = new TestListener();
        $notifier->notify($listener, new TestEvent());

        $this->assertSame(1, $listener->test1);
        $this->assertSame(0, $listener->test2);
    }

    /**
     * @covers ::notify
     * @covers ::supports
     * @covers ::call
     */
    public function test_notify_using_inheritance(): void
    {
        $notifier = new Notifier();
        $listener = new TestListener();
        $notifier->notify($listener, new TestEvent());
        $notifier->notify($listener, new TestEvent2());

        $this->assertSame(2, $listener->test1);
        $this->assertSame(1, $listener->test2);
    }

    /**
     * @covers ::notify
     * @covers ::supports
     * @covers ::call
     */
    public function test_notify_invalid_listener(): void
    {
        $notifier = new Notifier();
        $listener = new InvalidListener();
        $notifier->notify($listener, new TestEvent());
        $notifier->notify($listener, new TestEvent2());

        $this->assertNoException();
    }

    private function assertNoException(): void
    {
        $this->addToAssertionCount(1);
    }
}
