<?php

declare(strict_types=1);

namespace PHPExtra\EventManager;

class TestListener implements Listener
{
    public $test1 = 0;
    public $test2 = 0;

    public function handleTest1(TestEvent $event): void
    {
        ++$this->test1;
    }

    public function handleTest2(TestEvent2 $event): void
    {
        ++$this->test2;
    }
}
