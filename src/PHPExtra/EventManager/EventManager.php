<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Worker\SortableWorkerQueue;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\ListenerInterface;
use PHPExtra\EventManager\Worker\WorkerFactory;
use PHPExtra\EventManager\Worker\WorkerInterface;
use PHPExtra\EventManager\Worker\WorkerQueueInterface;
use PHPExtra\EventManager\Worker\WorkerResult;

/**
 * The EventManager class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManager implements EventManagerInterface
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
        $this->workerQueue = new SortableWorkerQueue();
    }

    /**
     * {@inheritdoc}
     */
    public function trigger(EventInterface $event)
    {
        $listenersFound = 0;

        $previousRunningEvent = $this->getRunningEvent();
        $this->setRunningEvent($event);

        $workers = $this->getWorkerQueue()->getWorkers();

        foreach ($workers as $worker) {
            if ($worker->isListeningTo($event)) {
                $this->runWorker($worker, $event);
                $listenersFound++;
            }
        }

        if ($listenersFound == 0) {
            // @todo log
        }

        if($previousRunningEvent){
            $this->setRunningEvent($previousRunningEvent);
        }

        return $this;
    }

    /**
     * @param WorkerInterface $worker
     * @param EventInterface  $event
     * @throws \Exception
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
     * {@inheritdoc}
     */
    public function addListener(ListenerInterface $listener, $priority = null)
    {
        foreach($this->getWorkerFactory()->createWorkers($listener) as $worker){
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
        $this->getWorkerQueue()->addWorker($worker);

        return $this;
    }

    /**
     * {@inheritdoc}
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
    public function setRunningEvent(EventInterface $runningEvent)
    {
        $this->runningEvent = $runningEvent;
    }

    /**
     * {@inheritdoc}
     */
    public function setThrowExceptions($throwExceptions)
    {
        $this->throwExceptions = $throwExceptions;

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}