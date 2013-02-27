<?php

use Skajdo\EventManager\Event\CancellableEventInterface;

class CancellableEventD implements CancellableEventInterface
{
    /**
     * Tell if current event is cancelled
     * If the event is cancelled it cannot be undone.
     * Each listener should ALWAYS check if the task was cancelled
     * before modyfying it.
     *
     * @return bool
     */
    public function isCancelled()
    {
        // TODO: Implement isCancelled() method.
    }

    /**
     * Cancel event
     *
     * @return CancellableEventInterface
     */
    public function setIsCancelled()
    {
        // TODO: Implement setIsCancelled() method.
    }

    /**
     * Tell if current event is cancelled
     *
     * @see CancellableEvent::isCancelled()
     * @return bool
     */
    public function getIsCancelled()
    {
        // TODO: Implement getIsCancelled() method.
    }}