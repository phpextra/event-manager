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
class AbstractCancellableEvent implements CancellableEvent
{
    /**
     * @var bool
     */
    private $isCancelled = false;

    /**
     * {@inheritdoc}
     */
    public function isCancelled()
    {
        return $this->isCancelled === true;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCancelled()
    {
        $this->isCancelled = true;

        return $this;
    }
}