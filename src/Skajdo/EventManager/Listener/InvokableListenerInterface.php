<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Listener;
use Skajdo\EventManager\EventInterface;

/**
 * Listener that can be invoked by a worker using invoke
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface InvokableListenerInterface extends ListenerInterface
{
    /**
     * Invoke an event
     *
     * @param EventInterface $event
     * @return void
     */
    public function invoke(EventInterface $event);
}