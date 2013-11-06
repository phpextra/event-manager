<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Listener;

/**
 * Normalized listener that can return us a list of method to event pairs along with priority for each pair
 */
interface NormalizedListenerInterface extends ListenerInterface
{
    /**
     * Return event class name paired with method that should be called for that event
     * Each method is aware of its listener
     *
     * @return ListenerMethod[]
     */
    public function getListenerMethods();
}