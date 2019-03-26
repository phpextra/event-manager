<?php

declare(strict_types=1);

namespace PHPExtra\EventManager;

/**
 * @internal This class is for internal use only. You should not depend on it in your code.
 */
final class Notifier
{
    public function notify(Listener $listener, Event $event): void
    {
        $eventClass = get_class($event);
        $reflection = new \ReflectionObject($listener);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($this->supports($method, $eventClass)) {
                $this->call($listener, $method->getName(), $event);
            }
        }
    }

    private function supports(\ReflectionMethod $method, string $eventClass): bool
    {
        if ($method->getNumberOfParameters() !== 1) {
            return false;
        }

        $paramClass = $method->getParameters()[0]->getClass();

        if ($paramClass === null) {
            return false;
        }

        if (!is_a($eventClass, $paramClass->getName(), true)) {
            return false;
        }

        return true;
    }

    private function call(Listener $listener, string $method, Event $event): void
    {
        $listener->{$method}($event);
    }
}
