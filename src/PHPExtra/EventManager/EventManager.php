<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Exception\RuntimeException;
use PHPExtra\EventManager\Listener\ListenerInterface;
use PHPExtra\EventManager\Worker\SortableWorkerQueue;
use PHPExtra\EventManager\Worker\WorkerFactory;
use PHPExtra\EventManager\Worker\WorkerFactoryInterface;
use PHPExtra\EventManager\Worker\WorkerInterface;
use PHPExtra\EventManager\Worker\WorkerQueueInterface;
use PHPExtra\EventManager\Worker\WorkerResult;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The EventManager class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManager implements EventManagerInterface
{
    /**
     * @var WorkerFactoryInterface
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
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function trigger(EventInterface $event)
    {
        $workerQueue = clone $this->getWorkerQueue();

        if($workerQueue->count() > 0){

            $previousRunningEvent = $this->getRunningEvent();
            $this->setRunningEvent($event);

            foreach ($workerQueue as $worker) {
                if ($worker->isListeningTo($event)) {
                    $this->runWorker($worker, $event);
                }
            }

            $this->setRunningEvent($previousRunningEvent);

        }else{
            $this->getLogger()->info(sprintf('Event %s has no workers', get_class($event)));
        }

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
     * @param EventInterface $event
     *
     * @return $this
     */
    public function setRunningEvent(EventInterface $event = null)
    {
        if($event === null){
            $this->getLogger()->debug('Current running event is now NULL');
        }else{
            $this->getLogger()->debug(sprintf('Current running event was set to %s', get_class($event)));
        }

        $this->runningEvent = $event;
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
     * @param WorkerInterface $worker
     * @param EventInterface  $event
     *
     * @throws RuntimeException
     * @return WorkerResult
     */
    protected function runWorker(WorkerInterface $worker, EventInterface $event)
    {
        $this->getLogger()->debug(sprintf('Starting worker #%s with priority %s for event %s', $worker, $worker->getPriority(), get_class($event)));

        $result = $worker->run($event);
        if (!$result->isSuccessful()) {
            $this->getLogger()->warning(sprintf('Worker #%s failed: %s', $worker, $result->getMessage()));
            if ($this->getThrowExceptions()) {
                $this->getLogger()->debug(sprintf('Throwing exception (throwExceptions is set to true)', $worker));

                $exception = new RuntimeException(sprintf('Worker #%s failed', $worker), 0, $result->getException());
                $exception
                    ->setEvent($event)
                    ->setListener($worker->getListener())
                ;

                throw $exception;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getThrowExceptions()
    {
        return $this->throwExceptions;
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
    public function addListener(ListenerInterface $listener, $priority = null)
    {
        $workers = $this->getWorkerFactory()->createWorkers($listener);
        $workersCount = 0;

        if ($priority !== null) {
            $this->getLogger()->debug(sprintf('Overriding the priority for all workers to %s in %s', $priority, get_class($listener)));
        }

        foreach ($workers as $worker) {
            if ($priority !== null) {
                $worker->setPriority($priority);
            }
            $this->addWorker($worker);
            $workersCount++;
        }

        if($workersCount == 0){
            $this->getLogger()->warning(sprintf('Listener "%s" does not have any workers', get_class($listener)));
        }

        return $this;
    }

    /**
     * Get worker factory
     *
     * @return WorkerFactoryInterface
     */
    public function getWorkerFactory()
    {
        return $this->workerFactory;
    }

    /**
     * @param WorkerInterface $worker
     *
     * @return $this
     */
    protected function addWorker(WorkerInterface $worker)
    {
        $params = array($worker, $worker->getListenerClass(), $worker->getMethodName(), $worker->getPriority());
        $this->getLogger()->debug(vsprintf('Added new worker (#%s) %s::%s() with priority: %s', $params));

        $this->getWorkerQueue()->addWorker($worker);

        return $this;
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