<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Skajdo\EventManager\Listener\ListenerInterface;
use Skajdo\EventManager\Listener\ListenerProxy;
use Skajdo\EventManager\Worker\Worker;
use Skajdo\EventManager\Worker\WorkerFactory;
use Skajdo\EventManager\Worker\WorkerQueue;

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
     */
    public function triggerEvent(EventInterface $event, $preDispatchHook = null, $postDispatchHook = null)
    {
        return $this->trigger($event);
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

        $this->recurrencyCheck($event);

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
     * @param EventInterface $event
     * @throws \RuntimeException If recurrency was detected
     */
    protected function recurrencyCheck(EventInterface $event)
    {
        if($this->getCallGraph()->hasObject($event)){
            $this->getLogger()->critical(Message::format(Message::RECURRENCY_DETECTED, $event));
            throw new \RuntimeException('Recurrency');
        }else{
            $this->callGraph->addObject($event);
        }
    }

    /**
     * Add event listener
     * Priority used in the listener can be overridden by setting the $priority argument
     *
     * @param ListenerInterface $listener
     * @param int $priority Default priority
     * @return $this
     */
    public function addListener(ListenerInterface $listener, $priority = null)
    {
        // normalize listener
        $listener = new ListenerProxy($listener);

        // create workers and add them to the worker queue
        foreach($listener->getListenerMethods() as $method){

            $worker = WorkerFactory::create($method);
            if($priority !== null){
                $worker->setPriority($priority);
            }

            $this->getLogger()->debug(sprintf('Worker added to the Queue'));
            $this->workers->insert($worker, $worker->getPriority());
        }

        return $this;
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
    public function getLogger()
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