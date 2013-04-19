<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Event;

/**
 * Represents cancellable event
 * Every cancellable event should extend this class or implement the
 * cancellable interface.
 *
 * @author      Jacek Kobus
 */
class CancellableEvent extends Event implements CancellableEventInterface
{
    /**
     * @var bool
     */
    private $isCancelled = false;

    /**
     * Tell if current event is cancelled
     * If the event is cancelled it cannot be undone.
     * Each listener should ALWAYS check if the task was cancelled
     * before modyfying it.
     *
     * @return bool
     */
    final public function isCancelled()
    {
        return $this->isCancelled === true;
    }

    /**
     * Cancel event
     *
     * @return CancellableEventInterface
     */
    final public function setIsCancelled()
    {
        $this->isCancelled = true;

        return $this;
    }

    /**
     * Tell if current event is cancelled
     *
     * @see CancellableEvent::isCancelled()
     * @return bool
     */
    final public function getIsCancelled()
    {
        return $this->isCancelled();
    }
}