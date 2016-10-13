<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Exception\ListenerException;

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
     * @throws ListenerException Exception might be thrown during unsuccessful listener run
     *
     * @return void
     */
    public function emit(Event $event);
}