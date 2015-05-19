<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Event\EventInterface;

/**
 * The WorkerResult class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
final class WorkerResult
{
    /**
     * @see WorkerResultStatus
     * @var int
     */
    private $status;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var WorkerInterface
     */
    private $worker;

    /**
     * @var EventInterface
     */
    private $event;

    /**
     * @param WorkerInterface $worker
     * @param EventInterface  $event
     * @param int             $status
     * @param \Exception      $exception
     */
    public function __construct(
        WorkerInterface $worker,
        EventInterface $event,
        $status = WorkerResultStatus::FAILURE,
        \Exception $exception = null
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
        return $this->getStatus() == 0;
    }

    /**
     * @see WorkerResultStatus
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}