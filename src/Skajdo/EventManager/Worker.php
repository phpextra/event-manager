<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

/**
 * Queue item.
 *
 * @author Jacek Kobus
 */
class Worker
{
    /**
     * @var ListenerInterface|\Closure
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
     *
     * @param ListenerInterface|\Closure $listener
     * @param string $method
     * @param string $eventClass
     * @param int $priority
     * @throws \InvalidArgumentException If Listener is not an instance of Listener interface nor Closure
     */
    public function __construct($listener, $method, $eventClass, $priority = Priority::NORMAL)
    {
        $this->setListener($listener);
        $this->setMethod($method);
        $this->setEventClass($eventClass);
        $this->setPriority($priority);
    }

    /**
     * @param EventInterface $event
     * @return WorkerResult
     */
    public function run(EventInterface $event)
    {
        try{
            $start = microtime(true);
            call_user_func(array($this->getListener(), $this->getMethod()), $event);
            $timeTaken = bcsub(microtime(true), $start, 10);
            return new WorkerResult(WorkerResultStatus::SUCCESS, $timeTaken);
        }catch (\Exception $e){
            return new WorkerResult(WorkerResultStatus::FAILURE, null, $e);
        }
    }

    /**
     * Tell if current worker is listening to given event type
     *
     * @param EventInterface $event
     * @return bool
     */
    public function isListeningTo(EventInterface $event)
    {
        return is_a($event, $this->getEventClass());
    }

    /**
     * @param string $eventClass
     */
    public function setEventClass($eventClass)
    {
        $this->eventClass = $eventClass;
    }

    /**
     * @return string
     */
    public function getEventClass()
    {
        return $this->eventClass;
    }

    /**
     * @param \Skajdo\EventManager\ListenerInterface $listener
     */
    public function setListener($listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return \Skajdo\EventManager\ListenerInterface|\Closure
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}