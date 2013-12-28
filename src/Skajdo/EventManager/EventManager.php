<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Skajdo\EventManager\Listener\ListenerInterface;
use Skajdo\EventManager\Listener\NormalizedListenerInterface;
use Skajdo\EventManager\Listener\ReflectedListener;
use Skajdo\EventManager\Worker\WorkerFactory;
use Skajdo\EventManager\Worker\WorkerInterface;
use Skajdo\EventManager\Worker\WorkerQueue;
use Skajdo\EventManager\Worker\WorkerQueueInterface;
use Skajdo\EventManager\Worker\WorkerResult;

/**
 * The EventManager class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManager implements LoggerAwareInterface
{
    /**
     * @var WorkerFactory
     */
    protected $workerFactory = null;

    /**
     * @var WorkerQueueInterface
     */
    protected $workerQueue = array();

    /**
     * Whenever to throw exceptions caught from listeners or not
     *
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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Create new event manager
     */
    public function __construct()
    {
        $this->workerFactory = new WorkerFactory();
        $this->workerQueue = new WorkerQueue();
    }

    /**
     * Calls all listeners that listen to given $event
     *
     * @param EventInterface $event
     * @throws \RuntimeException
     * @throws \Exception
     * @return $this
     */
    public function trigger(EventInterface $event)
    {
        $listenersFound = 0;
        $this->setRunningEvent($event);

        foreach ($this->getWorkerQueue() as $worker) {
            if ($worker->isListeningTo($event)) {
                $this->runWorker($worker, $event);
                $listenersFound++;
            }
        }

        if ($listenersFound == 0) {
            // @todo log
        }

        $this->setRunningEvent(null);

        return $this;
    }

    /**
     * @param WorkerInterface $worker
     * @param EventInterface  $event
     * @throws Exception
     * @return WorkerResult
     */
    protected function runWorker(WorkerInterface $worker, EventInterface $event)
    {
        $result = $worker->run($event);
        if (!$result->isSuccessful()) {
            if ($this->getThrowExceptions()) {
                throw $result->getException();
            }
        }

        return $result;
    }

    /**
     * Add event listener
     * Priority used in the listener can be overridden by setting the $priority
     *
     * @param ListenerInterface $listener
     * @param int               $priority
     * @return $this
     */
    public function addListener(ListenerInterface $listener, $priority = null)
    {
        if (!$listener instanceof NormalizedListenerInterface) {
            $listener = new ReflectedListener($listener);
        }

        // create workers and add them to the queue
        foreach ($listener->getListenerMethods() as $method) {
            $worker = $this->getWorkerFactory()->createWorker($method);
            if ($priority !== null) {
                $worker->setPriority($priority);
            }
            $this->addWorker($worker);
        }

        return $this;
    }

    /**
     * @param WorkerInterface $worker
     * @return $this
     */
    protected function addWorker(WorkerInterface $worker)
    {
        $this->getWorkerQueue()->add($worker);

        return $this;
    }

    /**
     * Return event that is currently running or null if no event is running
     *
     * @return EventInterface|null
     */
    public function getRunningEvent()
    {
        return $this->runningEvent;
    }

    /**
     * @param EventInterface $runningEvent
     *
     * @return $this
     */
    public function setRunningEvent($runningEvent)
    {
        $this->runningEvent = $runningEvent;
    }

    /**
     * If this is set to true all exceptions will be thrown
     * and the queue will be interrupted (incomplete)
     *
     * Defaults to FALSE
     *
     * @param bool $throwExceptions
     * @return EventManager
     */
    public function setThrowExceptions($throwExceptions)
    {
        $this->throwExceptions = $throwExceptions;

        return $this;
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
     * Get worker factory
     *
     * @return WorkerFactory
     */
    public function getWorkerFactory()
    {
        return $this->workerFactory;
    }

    /**
     * Get workers queue
     *
     * @return WorkerQueueInterface|WorkerInterface[]
     */
    public function getWorkerQueue()
    {
        return $this->workerQueue;
    }

    /**
     * @param WorkerQueueInterface $workerQueue
     *
     * @return $this
     */
    public function setWorkerQueue($workerQueue)
    {
        $this->workerQueue = $workerQueue;
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