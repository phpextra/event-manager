<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

/**
 * The Message class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class Message
{
    const RECURRENCY_DETECTED = 'Recurrency on event "%s" was detected and manager will stop propagation of event';

    /**
     * @see sprintf
     * @param string $message
     * @param mixed  $_
     *
     * @return string
     */
    public static function format($message, $_)
    {
        $args = func_get_args();
        array_shift($args);

        foreach ($args as &$arg) {
            if (is_object($arg)) {
                $arg = get_class($arg);
            }
        }

        return vsprintf($message, $args);
    }
}