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
     * @var string
     */
    private $id;

    /**
     * @var ListenerInterface
     */
    private $listener;

    /**
     * @var string
     */
    private $eventClass;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var string
     */
    private $methodName;

    /**
     * Create new worker that will wake-up listener using event
     * If priority is null the default (normal) will be used
     *
     * @param string            $id
     * @param ListenerInterface $listener
     * @param string            $methodName
     * @param string            $eventClass
     * @param int               $priority
     *
     */
    public function __construct($id, ListenerInterface $listener, $methodName, $eventClass, $priority = null)
    {
        if ($priority === null) {
            $priority = Priority::NORMAL;
        }

        $this->id = $id;

        $this->setListener($listener);
        $this->setMethodName($methodName);
        $this->setEventClass($eventClass);
        $this->setPriority($priority);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
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
    public function getListenerClass()
    {
        return get_class($this->getListener());
    }

    /**
     * @param ListenerInterface $listener
     */
    public function setListener(ListenerInterface $listener)
    {
        $this->listener = $listener;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->getMethodName();
    }

    /**
     * @deprecated use setMethodName
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        return $this->setMethodName($method);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @param string $methodName
     *
     * @return $this
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isListeningTo(EventInterface $event)
    {
        return is_a($event, $this->getEventClass());
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getId();
    }
}