<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use Closure;
use PHPExtra\EventManager\Listener\AnonymousListener;
use PHPExtra\EventManager\Listener\ListenerInterface;
use PHPExtra\EventManager\Listener\ListenerMethod;

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

            $workers[] = new Worker($listener, $method->getName(), $eventClassName);
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

//    /**
//     * Try to find a priority for given method
//     *
//     * @param \ReflectionMethod $method
//     * @return int|null
//     */
//    protected function getPriority(\ReflectionMethod $method)
//    {
//        $priority = null;
//        //@todo get priority
////        if($method->getDocBlock() !== false){
////            /** @var $tag \Zend\Code\Reflection\DocBlock\Tag\GenericTag */
////            $tag = $method->getDocBlock()->getTag('priority');
////
////            if($tag !== false){
////                if(is_numeric($tag->getContent())){
////                    $priority = (int)$tag->getContent();
////                }else{
////                    $priority = Priority::getPriorityByName($tag->getContent());
////                }
////            }
////        }
//        return $priority;
//    }
}