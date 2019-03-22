<?php

declare(strict_types=1);

namespace PHPExtra\EventManager;

class InvalidListener implements Listener
{
    public function handleTest1($event): void
    {
        throw new \RuntimeException('This should not be called');
    }

    public function handleTest2(): void
    {
        throw new \RuntimeException('This should not be called');
    }

    public function handleTest3(\stdClass $class): void
    {
        throw new \RuntimeException('This should not be called');
    }
}
