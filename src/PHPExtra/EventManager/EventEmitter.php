<?php

declare(strict_types=1);

namespace PHPExtra\EventManager;

interface EventEmitter
{
    public function add(Listener $listener): void;

    public function emit(Event $event): void;
}
