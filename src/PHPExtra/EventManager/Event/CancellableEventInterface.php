<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Event;

/**
 * The CancellableEventInterface interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface CancellableEventInterface extends EventInterface
{
    /**
     * Tell if current event was cancelled
     * If the event is cancelled it cannot be undone.
     * Each listener should ALWAYS check if the task was cancelled
     * before modifying it.
     *
     * @return bool
     */
    public function isCancelled();

    /**
     * Cancel event
     *
     * @return $this
     */
    public function setIsCancelled();
}