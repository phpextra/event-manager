<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Exception\EventException;

/**
 * The EventEmitter interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface EventEmitter
{
    /**
     * Emit event to all matching listeners
     *
     * @param Event $event The event
     *
     * @throws EventException Exception might be thrown during unsuccessful worker run
     *
     * @return void
     */
    public function emit(Event $event);
}