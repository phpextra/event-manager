<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

/**
 * Represents cancellable event
 * Every cancellable event should extend this class or implement the
 * cancellable interface.
 *
 * @author      Jacek Kobus
 */
class AbstractCancellableEvent extends Event implements CancellableEventInterface
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