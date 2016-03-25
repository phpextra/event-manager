<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Listener\Listener;
use PHPExtra\EventManager\Priority;

/**
 * The Worker class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class Worker
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Listener
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
     * @param Listener $listener
     * @param string            $methodName
     * @param string            $eventClass
     * @param int               $priority
     *
     */
    public function __construct($id, Listener $listener, $methodName, $eventClass, $priority = null)
    {
        if ($priority === null) {
            $priority = Priority::NORMAL;
        }

        $this->id = $id;
        $this->listener = $listener;
        $this->methodName = $methodName;
        $this->eventClass = $eventClass;
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function run(Event $event)
    {
        try {
            call_user_func(array($this->getListener(), $this->getMethod()), $event);
            $result = new WorkerResult($this, $event);
        } catch (\Exception $e) {
            $result = new WorkerResult($this, $event, $e);
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
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->getMethodName();
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
    public function getEventClass()
    {
        return $this->eventClass;
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