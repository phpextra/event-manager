<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager\Event;

/**
 * Concrete implementation of cancellable event
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class CancellableEvent implements CancellableEventInterface
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