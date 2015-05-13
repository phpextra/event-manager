<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Listener\AnonymousListenerInterface;
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
     * Generate ID for new worker created in the factory
     *
     * @return int
     */
    private function generateWorkerId()
    {
        return $this->nextWorkerId++;
    }

    /**
     * {@inheritdoc}
     */
    public function createWorkers(ListenerInterface $listener, $priority = null)
    {
        if($listener instanceof AnonymousListenerInterface){
            $workers = $this->createWorkersFromAnonymousListener($listener, $priority);
        }else{
            $workers = $this->createWorkersFromStandardListener($listener, $priority);
        }
        return $workers;
    }

    /**
     * @param ListenerInterface $listener
     * @param int              $priority
     *
     * @return array
     */
    private function createWorkersFromStandardListener(ListenerInterface $listener, $priority = null)
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

            if($priority === null){
                $workerPriority = PriorityResolver::getPriorityFromDocComment($method->getDocComment(), Priority::NORMAL);
            }else{
                $workerPriority = $priority;
            }

            if($priority === null){}

            $workers[] = new Worker($this->generateWorkerId(), $listener, $method->getName(), $eventClassName, $workerPriority);
        }

        return $workers;
    }

    /**
     * Priority given here overrides the priority set within the anonymous listener
     *
     * @param AnonymousListenerInterface $listener
     * @param int $priority
     * @return WorkerInterface[]
     */
    private function createWorkersFromAnonymousListener(AnonymousListenerInterface $listener, $priority = null)
    {
        $closureReflection = new \ReflectionMethod($listener->getClosure(), '__invoke');
        $params = $closureReflection->getParameters();

        if($priority === null){
            $priority = $listener->getPriority();
        }

        if (isset($params[0])) {
            $param = $params[0];
            $eventClassName = $this->getEventClassNameFromParam($param);

            if ($eventClassName === null) {
                $message = sprintf('First closure param (%s) must be a class implementing an EventInterface', $param->getName());
                throw new \InvalidArgumentException($message);
            }

            return array(new AnonymousWorker($this->generateWorkerId(), $listener, $eventClassName, $priority));
        }

        return array();
    }

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
}