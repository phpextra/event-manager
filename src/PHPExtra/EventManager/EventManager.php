<?php

declare(strict_types=1);

namespace PHPExtra\EventManager;

final class EventManager implements EventEmitter
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @var Notifier
     */
    private $notifier;

    public function __construct()
    {
        $this->notifier = new Notifier();
    }

    public function emit(Event $event): void
    {
        foreach ($this->listeners as $listener) {
            $this->notifier->notify($listener, $event);
        }
    }

    public function add(Listener $listener): void
    {
        $this->listeners[] = $listener;
    }
}
