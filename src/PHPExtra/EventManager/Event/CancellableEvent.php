<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Event;

/**
 * Represents an ongoing event that can be cancelled.
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
abstract class CancellableEvent implements Event
{
    /**
     * @var bool
     */
    private $isCancelled = false;

    /**
     * @var string
     */
    private $reason;

    /**
     * Tell if current event was cancelled. If the event was cancelled it cannot be undone.
     * Each listener should ALWAYS check if the task was cancelled before modifying it.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->isCancelled;
    }

    /**
     * Mark event as cancelled
     *
     * @param string $reason
     */
    public function cancel($reason = null)
    {
        $this->reason = $reason;
        $this->isCancelled = true;
    }

    /**
     * Retrieve a message about why event was cancelled.
     * Reason is optional.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}