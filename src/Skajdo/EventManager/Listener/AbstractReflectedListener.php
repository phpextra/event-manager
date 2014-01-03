<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager\Listener;

/**
 * Extract common reflection tasks
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class AbstractReflectedListener
{
    /**
     * @param \ReflectionParameter $param
     * @return null|string
     */
    protected function getEventClassNameFromParam(\ReflectionParameter $param)
    {
        $eventClassName = null;
        $eventClass = $param->getClass();

        if($eventClass !== null){
            $eventClassName = $eventClass->getName();
            $requiredInterface = 'Skajdo\EventManager\Event\EventInterface';
            if (!is_subclass_of($eventClassName, $requiredInterface) && $eventClassName != $requiredInterface) {
                $eventClassName = null;
            }
        }
        return $eventClassName;
    }
}