<?php

namespace Skajdo\EventManager;

class WorkerFactory
{
    public static function create(ListenerInterface $listener)
    {
        new Worker($listener, $listener->getMethodName(), $listener->getEventClassName(), $listener->getPriority());
    }
}