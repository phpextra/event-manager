<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Event\Event;

/**
 * The WorkerResult class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
final class WorkerResult
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var Worker
     */
    private $worker;

    /**
     * @var Event
     */
    private $event;

    /**
     * @param Worker     $worker
     * @param Event      $event
     * @param \Exception $exception
     */
    public function __construct(Worker $worker, Event $event, \Exception $exception = null)
    {
        $this->event = $event;
        $this->worker = $worker;
        $this->exception = $exception;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return Worker
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * Get message returned by exception or null if no exception
     *
     * @return string
     */
    public function getMessage()
    {
        if ($this->getException()) {
            return $this->getException()->getMessage();
        }

        return null;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getException() === null;
    }
}