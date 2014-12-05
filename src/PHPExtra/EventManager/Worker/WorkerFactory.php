<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Listener\AnonymousListener;
use PHPExtra\EventManager\Listener\Listener;
use PHPExtra\EventManager\Priority;
use PHPExtra\EventManager\PriorityResolver;

/**
 * The WorkerFactory class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class WorkerFactory
{
    /**
     * @var int
     */
    private $nextWorkerId = 1;

    /**
     * @var PriorityResolver
     */
    private $priorityResolver;

    /**
     * The WorkerFactory constructor.
     *
     * @param PriorityResolver $priorityResolver
     */
    public function __construct(PriorityResolver $priorityResolver = null)
    {
        if(!$priorityResolver){
            $this->priorityResolver = new PriorityResolver();
        }
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

    /**
     * {@inheritdoc}
     */
    public function createWorkers(Listener $listener, $priority = null)
    {
        if($listener instanceof AnonymousListener){
            $workers = $this->createWorkersFromAnonymousListener($listener, $priority);
        }else{
            $workers = $this->createWorkersFromStandardListener($listener, $priority);
        }
        return $workers;
    }

    /**
     * @param Listener $listener
     * @param int              $priority
     *
     * @return array
     */
    private function createWorkersFromStandardListener(Listener $listener, $priority = null)
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
                $workerPriority = $this->priorityResolver->getPriorityFromDocComment($method->getDocComment());

                if($workerPriority === null){
                    $workerPriority = Priority::NORMAL;
                }

            }else{
                $workerPriority = $priority;
            }

            $workers[] = new Worker($this->generateWorkerId(), $listener, $method->name, $eventClassName, $workerPriority);
        }

        return $workers;
    }

    /**
     * Priority given here overrides the priority set within the anonymous listener
     *
     * @param AnonymousListener $listener
     * @param int $priority
     * @return Worker[]
     */
    private function createWorkersFromAnonymousListener(AnonymousListener $listener, $priority = null)
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
                $message = sprintf('First closure param (%s) must be a class implementing an Event', $param->name);
                throw new \InvalidArgumentException($message);
            }

            return array(new Worker($this->generateWorkerId(), $listener, 'invoke', $eventClassName, $priority));
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
            $eventClassName = $eventClass->name;
            $requiredInterface = 'PHPExtra\EventManager\Event\Event';
            if (!$eventClass->implementsInterface($requiredInterface) && $eventClassName != $requiredInterface) {
                $eventClassName = null;
            }
        }

        return $eventClassName;
    }
}