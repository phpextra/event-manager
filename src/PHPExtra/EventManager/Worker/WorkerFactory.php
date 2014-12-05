<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Listener\AnonymousListener;
use PHPExtra\EventManager\Listener\ListenerInterface;
use PHPExtra\EventManager\Priority;
use PHPExtra\EventManager\PriorityResolver;

/**
 * The WorkerFactory class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerFactory implements WorkerFactoryInterface
{
    /**
     * @var int
     */
    private $nextWorkerId = 1000;

    /**
     * {@inheritdoc}
     */
    public function createWorkers(ListenerInterface $listener)
    {
        return $this->createWorkersFromListener($listener);
    }

    /**
     * Generate ID for new worker created in the factory
     *
     * @return int
     */
    private function generateWorkerId()
    {
        return $this->nextWorkerId++;
    }

//    /**
//     * @param AnonymousListener $listener
//     *
//     * @return WorkerInterface[]
//     * @throws \InvalidArgumentException
//     */
//    private function createWorkersFromAnonymousListener(AnonymousListener $listener)
//    {
//        $closureReflection = new \ReflectionMethod($listener->getClosure(), '__invoke');
//        $params = $closureReflection->getParameters();
//
//        if (isset($params[0])) {
//            $param = $params[0];
//            $eventClassName = $this->getEventClassNameFromParam($param);
//
//            if ($eventClassName === null) {
//                $message = sprintf('First closure param (%s) must be a class implementing an EventInterface', $param->getName());
//                throw new \InvalidArgumentException($message);
//            }
//
//            return array(new Worker($this->generateWorkerId(), $listener, 'invoke', $eventClassName, $listener->getPriority()));
//        }
//
//        return array();
//    }

    /**
     * @param \ReflectionParameter $param
     *
     * @return null|string
     */
    private function getEventClassNameFromParam(\ReflectionParameter $param)
    {
        $eventClassName = null;
        $eventClass = $param->getClass();

        if ($eventClass !== null) {
            $eventClassName = $eventClass->getName();
            $requiredInterface = 'PHPExtra\EventManager\Event\EventInterface';
            if (!is_subclass_of($eventClassName, $requiredInterface) && $eventClassName != $requiredInterface) {
                $eventClassName = null;
            }
        }

        return $eventClassName;
    }

    /**
     * @param ListenerInterface $listener
     *
     * @return WorkerInterface[]
     */
    private function createWorkersFromListener(ListenerInterface $listener)
    {
        $workers = array();
        $reflectedListener = new \ReflectionClass($listenerClass = get_class($listener));
        foreach ($reflectedListener->getMethods() as $method) {

            if (!$method->isPublic() || ($method->getNumberOfParameters() > 1) || !($param = current($method->getParameters()))) {
                continue;
            }

            if (($eventClassName = $this->getEventClassNameFromParam($param)) === null) {
                continue;
            }

            $priority = PriorityResolver::getPriorityFromDocComment($method->getDocComment(), Priority::NORMAL);
            $workers[] = new Worker($this->generateWorkerId(), $listener, $method->getName(), $eventClassName, $priority);
        }

        return $workers;
    }
}