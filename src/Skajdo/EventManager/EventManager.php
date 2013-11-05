<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

use Closure;
use Psr\Log\NullLogger;
use Skajdo\EventManager\Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Skajdo\EventManager\Listener\AnonymousListener;
use Zend\Code\Reflection\ClassReflection;

/**
 * The event manager
 *
 * @author      Jacek Kobus
 */
class EventManager implements LoggerAwareInterface
{
    /**
     * @var WorkerQueue
     */
    protected $workers = array();

    /**
     * Whenever to throw exceptions caught from listeners or not
     * @var bool
     */
    protected $throwExceptions = false;

    /**
     * Currently running event
     *
     * @var EventInterface
     */
    protected $runningEvent = null;

    /**
     * Limit recursion
     *
     * @var int
     */
    protected $recurrencyLimit = 50;

    /**
     * Current recurrency level
     *
     * @var int
     */
    protected $recurrencyLevel = 0;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var CallGraph
     */
    protected $callGraph = null;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->workers = new WorkerQueue();
        if ($logger !== null) {
            $this->setLogger($logger);
        }
    }

    /**
     * @see EventManager::trigger()
     * @deprecated since 1.1.1; use EventManager::trigger() instead
     * @param \Skajdo\EventManager\EventInterface $event
     * @param callable $preDispatchHook
     * @param callable $postDispatchHook
     * @return EventManager
     */
    public function triggerEvent(EventInterface $event, $preDispatchHook = null, $postDispatchHook = null)
    {
        return $this->trigger($event, $preDispatchHook, $postDispatchHook);
    }

    /**
     * Trigger event; calls all listeners that listen to this event
     *
     * @param EventInterface $event
     * @throws \RuntimeException
     * @throws \Exception|null
     * @return EventManager
     * @return \Skajdo\EventManager\EventManager
     */
    public function trigger(EventInterface $event)
    {
        $this->runningEvent = $event;
        $eventClassName = get_class($event);
        $listenersFound = 0;
        $loopStart = microtime(true);

        if($this->getCallGraph()->hasObject($event)){
            $this->getLogger()->critical(sprintf('Recurrency on event "%s" was detected and manager will stop propagation of event', get_class($event)));
            throw new \RuntimeException('Recurrency');
        }else{
            $this->callGraph->addObject($event);
        }

        /* @var $worker Worker */
        foreach ($this->workers->getIterator() as $workerId => $worker) {

            if ($worker->isListeningTo($event)) {

                $this->getLogger()->debug(sprintf('Starting worker #%s with event %s', $workerId, get_class($event)));

                $result = $worker->run($event);
                if(!$result->isSuccessful()){
                    $this->getLogger()->critical(sprintf('Worker #%s failed to complete the task: (%s) %s', $workerId, $result->getExceptionClass(), $result->getMessage()));
                    if ($this->getThrowExceptions()) {
                        throw $result->getException();
                    }
                }else{
                    $this->getLogger()->debug(sprintf('Worker #%s completed in %s ms', $workerId, $result->getExecutionTime()));
                }
                $listenersFound++;
            }
        }

        $loopEnd = bcsub(microtime(true), $loopStart, 8);
        if ($listenersFound == 0) {
            $this->getLogger()->debug(sprintf('%s has no listeners', $eventClassName));
        } else {
            $this->getLogger()->debug(
                sprintf('Event %s was completed for %s listener(s) in %s s', $eventClassName, $listenersFound, $loopEnd)
            );
        }

        $this->getCallGraph()->clear();
        $this->runningEvent = null;

        return $this;
    }

    /**
     * Add event listener
     * Priority used in doc comments can be overridden by setting the $priority
     * Otherwise the setting from the doc comment will be used.
     *
     * @see Priority
     * @param ListenerInterface|\Closure $listener
     * @param int $priority Defaults to NORMAL(0)
     * @throws \InvalidArgumentException If Listener is not an instance of Listener interface nor Closure
     * @return EventManager
     */
    public function addListener($listener, $priority = null)
    {

//        $this->addWorker(WorkerFactory::create($listener));
//
//        if($listener instanceof AnonymousListener){
//            if($priority){
//                $listener->setPriority($priority);
//            }
//
//            new Worker($listener, $listener->getMethodName(), $listener->getEventClassName(), $listener->getPriority());
//
//
//        }

        // special treatment for closures
        if ($listener instanceof \Closure) {
            $closure = new \ReflectionFunction($listener);
            /* @var $param \Zend\Code\Reflection\ParameterReflection */
            $param = current($closure->getParameters());

            if (!$param || !($eventClassName = $this->_getEventClassName($param))) {
                $this->getLogger()->info(sprintf('Given closure does not listen to any known event'));
            } else {
                $this->_addListener($listener, '__invoke', $eventClassName, $priority);
            }

            return $this;
        }

        if (!$listener instanceof ListenerInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Listener must implement the Listener or it must be an instance of Closure but %s given',
                get_class($listener)
            ));
        }

        $listenerIsListeningToEvent = false;
        $a = new ClassReflection($listenerClass = get_class($listener));

        foreach ($a->getMethods() as $method) {

            /* @var $method \Zend\Code\Reflection\MethodReflection */
            if (($method->getNumberOfParameters() > 1) || !($param = current($method->getParameters()))) {
                continue;
            }

            /* @var $param \Zend\Code\Reflection\ParameterReflection */
            if (($eventClassName = $this->_getEventClassName($param)) === null) {
                continue;
            }

            /**
             * This is a workaround for zend code's bugged getDescription ...
             * At the moment ZF's 2 code library is not good to depend on.
             */

            if($method->getDocBlock() !== false){
                /** @var $tag \Zend\Code\Reflection\DocBlock\Tag\GenericTag */
                $tag = $method->getDocBlock()->getTag('priority');

                if($tag !== false){
                    if(is_numeric($tag->getContent())){
                        $priority = (int)$tag->getContent();
                    }else{
                        $priority = Priority::getPriorityByName($tag->getContent());
                    }
                }
            }

            $listenerIsListeningToEvent = true;
            $this->_addListener($listener, $method->getName(), $eventClassName, $priority);
        }

        if (!$listenerIsListeningToEvent) {
            $this->getLogger()->info(sprintf('Given Listener (%s) does not listen to any known events', $listenerClass));
        }

        return $this;
    }

    /**
     * @param Worker $worker
     * @return $this
     */
    protected function addWorker(Worker $worker)
    {
        $this->getLogger()->debug(sprintf('Adding worker to the queue'));
        $this->workers->insert($worker, $worker->getPriority());
        return $this;
    }

    /**
     * Internal method for adding listeners
     *
     * @deprecated
     * @see Priority
     * @param ListenerInterface|\Closure $listener
     * @param $listenerMethodName
     * @param $eventClassName
     * @param $listenerMethodName
     * @param int $priority Defaults to normal (int)0
     * @return EventManager
     */
    protected function _addListener($listener, $listenerMethodName, $eventClassName, $priority = null)
    {
        if($priority === null){
            $priority = Priority::NORMAL;
        }
        $listenerName = sprintf('%s::%s()', get_class($listener), $listenerMethodName);
        $this->getLogger()->debug(sprintf('%s is now listening to %s with priority %s', $listenerName, $eventClassName, $priority));
        $this->workers->insert(new Worker($listener, $listenerMethodName, $eventClassName, $priority), $priority);

        return $this;
    }

    /**
     * Return event class name for given method/function parameter
     * This method will return NULL if the class given in parameter is not
     * an Event nor one of its subclasses.
     *
     * @param \ReflectionParameter $param
     * @return null|string
     */
    protected function _getEventClassName(\ReflectionParameter $param)
    {
        if (!($eventClass = $param->getClass())) {
            return null;
        }

        $eventClassName = $eventClass->getName();
        $requiredInterface = 'Skajdo\EventManager\EventInterface';
        if (!is_subclass_of($eventClassName, $requiredInterface) && $eventClassName != $requiredInterface) {
            return null;
        }

        return $eventClassName;
    }

    /**
     * If this is set to true all exceptions will be thrown
     * and the queue will be interrupted (incomplete).
     *
     * Defaults to FALSE
     *
     * @param $throwExceptions
     * @return EventManager
     */
    public function setThrowExceptions($throwExceptions)
    {
        if ($throwExceptions == true) {
            $this->getLogger()->debug(
                sprintf('%s will now throw exceptions in case of listener failure', get_class($this))
            );
        } else {
            $this->getLogger()->debug(
                sprintf('%s will NOT THROW exceptions in case of listener failure', get_class($this))
            );
        }
        $this->throwExceptions = $throwExceptions;

        return $this;
    }

    /**
     * Return event that is currently running or NULL if no event is running
     *
     * @return null|EventInterface
     */
    public function getRunningEvent()
    {
        return $this->runningEvent;
    }

    /**
     * Tell if current instance of event manager will break the queue
     * if an exception will be thrown from listener.
     *
     * @return boolean
     */
    public function getThrowExceptions()
    {
        return $this->throwExceptions;
    }

    /**
     * Get call graph
     *
     * @return CallGraph
     */
    protected function getCallGraph()
    {
        if ($this->callGraph === null) {
            $this->callGraph = new CallGraph();
        }
        return $this->callGraph;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return EventManager
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}