<?php

namespace Skajdo\EventManager;

class Message
{

    /**
     * @param string $message
     * @param mixed $_ Use as many arguments as you want
     * @internal param $arguments
     * @return string
     */
    public static function format($message, $_)
    {
        $args = func_get_args();
        array_shift($args);
        return sprintf($message, $args);
    }
}