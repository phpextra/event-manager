<?php

namespace Skajdo\EventManager;

class Message
{

    const RECURRENCY_DETECTED = 'Recurrency on event "%s" was detected and manager will stop propagation of event';

    /**
     * @param string $message
     * @param mixed $_ Use as many arguments as you want
     * @return string
     */
    public static function format($message, $_)
    {
        $args = func_get_args();
        array_shift($args);

        foreach($args as &$arg){
            if(is_object($arg)){
                $arg = get_class($arg);
            }
        }

        return sprintf($message, $args);
    }
}