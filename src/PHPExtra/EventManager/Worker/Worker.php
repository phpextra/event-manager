<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\ListenerInterface;
use PHPExtra\EventManager\Priority;

/**
 * The Worker class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class Worker implements WorkerInterface
{
    /**
     * @var ListenerInterface
     */
    protected $listener;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $eventClass;

    /**
     * @var int
     */
    protected $priority;

    /**
     * Create new worker that will wake-up listener using event
     * If priority is null the default (normal) will be used
     *
     * @param ListenerInterface $listener
     * @param string            $method
     * @param string            $eventClass
     * @param int               $priority
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(ListenerInterface $listener, $method, $eventClass, $priority = null)
    {
        if ($priority === null) {
            $priority = Priority::NORMAL;
        }

        $this->setListener($listener);
        $this->setMethod($method);
        $this->setEventClass($eventClass);
        $this->setPriority($priority);
    }

    /**
     * @param EventInterface $event
     *
     * @return WorkerResult
     */
    public function run(EventInterface $event)
    {
        try {
            call_user_func(array($this->getListener(), $this->getMethod()), $event);
            $result = new WorkerResult($this, $event, WorkerResultStatus::SUCCESS);
        } catch (\Exception $e) {
            $result = new WorkerResult($this, $event, WorkerResultStatus::FAILURE, $e);
        }

        return $result;
    }

    /**
     * @return ListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param ListenerInterface $listener
     */
    public function setListener(ListenerInterface $listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Tell if current worker is listening to given event type
     *
     * @param EventInterface $event
     *
     * @return bool
     */
    public function isListeningTo(EventInterface $event)
    {
        return is_a($event, $this->getEventClass());
    }

    /**
     * @return string
     */
    public function getEventClass()
    {
        return $this->eventClass;
    }

    /**
     * @param string $eventClass
     */
    public function setEventClass($eventClass)
    {
        $this->eventClass = $eventClass;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }
}