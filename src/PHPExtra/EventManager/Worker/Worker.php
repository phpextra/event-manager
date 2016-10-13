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
     * @param string   $id
     * @param Listener $listener
     * @param string   $methodName
     * @param string   $eventClass
     * @param int      $priority
     */
    public function __construct($id, Listener $listener, $methodName, $eventClass, $priority = Priority::NORMAL)
    {
        $this->id = $id;
        $this->listener = $listener;
        $this->methodName = $methodName;
        $this->eventClass = $eventClass;
        $this->priority = $priority;
    }

    /**
     * @param Event $event
     *
     * @return WorkerResult
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get full worker name including class, method and priority.
     */
    public function getName()
    {
        return vsprintf('%s::%s()[P:%s]', array(
            $this->getListenerClass(),
            $this->getMethodName(),
            $this->getPriority()
        ));
    }

    /**
     * @return Listener
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @return string
     */
    public function getListenerClass()
    {
        return get_class($this->getListener());
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->getMethodName();
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function getEventClass()
    {
        return $this->eventClass;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param $priority
     *
     * @return $this
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
        return (string)$this->getName();
    }
}