<?php

namespace Skajdo\EventManager\Listener;

use Skajdo\EventManager\Listener\ListenerInterface;
use Skajdo\EventManager\Priority;

/**
 * Each listener can have many method to event pairs that are represented by this object.
 * Each pair can have different priority.
 */
class ListenerMethod
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
    function __construct(ListenerInterface $listener, $methodName, $eventClassName, $priority = Priority::NORMAL)
    {
        $this->listener = $listener;
        $this->eventClassName = $eventClassName;
        $this->methodName = $methodName;
        $this->priority = $priority;
    }

    /**
     * @return \Skajdo\EventManager\Listener\ListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @return string
     */
    public function getEventClassName()
    {
        return $this->eventClassName;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}