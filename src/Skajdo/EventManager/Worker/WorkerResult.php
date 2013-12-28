<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager\Worker;
use Skajdo\EventManager\EventInterface;
use Skajdo\EventManager\Exception;

/**
 * The WorkerResult class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerResult
{
    /**
     * @see WorkerResultStatus
     * @var int
     */
    protected $status;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var WorkerInterface
     */
    protected $worker;

    /**
     * @var EventInterface
     */
    protected $event;

    /**
     * @param WorkerInterface                $worker
     * @param EventInterface                 $event
     * @param int                            $status
     * @param Exception $exception
     */
    function __construct(
        WorkerInterface $worker,
        EventInterface $event,
        $status = WorkerResultStatus::FAILURE,
        Exception $exception = null
    ) {
        $this->event = $event;
        $this->worker = $worker;
        $this->exception = $exception;
        $this->status = $status;
    }

    /**
     * @return EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return WorkerInterface
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @see WorkerResultStatus
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
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
     * Returns null if no exception
     * @deprecated
     * @return string|null
     */
    public function getExceptionClass()
    {
        if ($this->getException()) {
            return get_class($this->getException());
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getStatus() == 0;
    }
}