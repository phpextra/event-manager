<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Listener\AnonymousListener;
use PHPExtra\EventManager\Listener\ListenerInterface;
use PHPExtra\EventManager\Priority;

/**
 * The WorkerFactory class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerFactory
{
    /**
     * @param ListenerInterface $listener
     * @return WorkerInterface[]
     */
    public static function createWorkers(ListenerInterface $listener)
    {
        if($listener instanceof AnonymousListener){
            $worker = self::createWorkersFromAnonymousListener($listener);
        }else{
            $worker = self::createWorkersFromListener($listener);
        }
        return $worker;
    }

    /**
     * @param AnonymousListener $listener
     * @return WorkerInterface[]
     * @throws \InvalidArgumentException
     */
    protected static function createWorkersFromAnonymousListener(AnonymousListener $listener)
    {
        $closureReflection = new \ReflectionMethod($listener->getClosure(), '__invoke');
        $params = $closureReflection->getParameters();

        if(isset($params[0])){
            $param = $params[0];
            $eventClassName = self::getEventClassNameFromParam($param);

            if ($eventClassName === null) {
                throw new \InvalidArgumentException(sprintf('First closure param (%s) must be a class implementing an EventInterface', $param->getName()));
            }
            return array(new Worker($listener, 'invoke', $eventClassName, $listener->getPriority()));
        }
        return array();
    }

    /**
     * @param ListenerInterface $listener
     * @return WorkerInterface[]
     */
    protected static function createWorkersFromListener(ListenerInterface $listener)
    {
        $workers = array();
        $reflectedListener = new \ReflectionClass($listenerClass = get_class($listener));
        foreach ($reflectedListener->getMethods() as $method) {

            if (($method->getNumberOfParameters() > 1) || !($param = current($method->getParameters()))) {
                continue;
            }

            if (($eventClassName = self::getEventClassNameFromParam($param)) === null) {
                continue;
            }

            $priority = self::getPriority($method);

            $workers[] = new Worker($listener, $method->getName(), $eventClassName, $priority);
        }
        return $workers;
    }

    /**
     * @param \ReflectionParameter $param
     * @return null|string
     */
    protected static function getEventClassNameFromParam(\ReflectionParameter $param)
    {
        $eventClassName = null;
        $eventClass = $param->getClass();

        if($eventClass !== null){
            $eventClassName = $eventClass->getName();
            $requiredInterface = 'PHPExtra\EventManager\Event\EventInterface';
            if (!is_subclass_of($eventClassName, $requiredInterface) && $eventClassName != $requiredInterface) {
                $eventClassName = null;
            }
        }
        return $eventClassName;
    }

    /**
     * Try to find a priority for given method
     *
     * @param \ReflectionMethod $method
     * @param int               $default
     *
     * @return int
     */
    protected static function getPriority(\ReflectionMethod $method, $default = Priority::NORMAL)
    {
        $priority = null;
        $pattern = '#@priority\\s+(\-?\d+|LOWEST|LOW|NORMAL|HIGH|HIGHEST|MONITOR)#i';

        $matches = array();
        preg_match($pattern, $method->getDocComment(), $matches);

        if(isset($matches[1])){
            if(is_numeric($matches[1])){
                $priority = (int)$matches[1];
            }else{
                $priority = Priority::getPriorityByName($matches[1]);
            }
        }

        return $priority === null ? $default : $priority;
    }
}