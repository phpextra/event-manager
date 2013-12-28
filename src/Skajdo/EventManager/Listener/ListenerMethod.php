<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager\Listener;

class ListenerMethod implements ListenerMethodInterface
{
    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var string
     */
    protected $eventClassName;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var ListenerInterface
     */
    protected $listener;

    /**
     * @param ListenerInterface $listener
     * @param string $methodName
     * @param string $eventClassName
     * @param int $priority
     */
    function __construct(ListenerInterface $listener, $methodName, $eventClassName, $priority = null)
    {
        $this->listener = $listener;
        $this->eventClassName = $eventClassName;
        $this->methodName = $methodName;
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventClassName()
    {
        return $this->eventClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }
}