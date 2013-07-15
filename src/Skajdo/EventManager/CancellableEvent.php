<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;
use Skajdo\EventManager\Event;

/**
 * Represents cancellable event
 *
 * @author      Jacek Kobus
 */
interface CancellableEvent extends Event
{
    /**
     * Tell if current event is cancelled
     * If the event is cancelled it cannot be undone.
     * Each listener should ALWAYS check if the task was cancelled
     * before modyfying it.
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