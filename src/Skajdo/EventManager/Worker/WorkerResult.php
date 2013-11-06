<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Worker;

/**
 * Worker execution result
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
     * @var float
     */
    protected $executionTime;

    /**
     * @param int $status
     * @param int $executionTime
     * @param \Exception $exception
     */
    function __construct($status = WorkerResultStatus::FAILURE, $executionTime = null, $exception = null)
    {
        $this->exception = $exception;
        $this->executionTime = $executionTime;
        $this->status = $status;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return string
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get message returned by exception
     *
     * @return null|string
     */
    public function getMessage()
    {
        if($this->getException()){
            return $this->getException()->getMessage();
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getExceptionClass()
    {
        if($this->getException()){
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