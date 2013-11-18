<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

/**
 * Represents cancellable event
 *
 * @author      Jacek Kobus
 */
class AbstractCancellableEvent implements CancellableEventInterface
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