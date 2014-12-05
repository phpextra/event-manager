<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Exception\ExceptionContext;
use PHPExtra\EventManager\Exception\RuntimeException;
use PHPExtra\EventManager\Listener\ListenerInterface;
use PHPExtra\EventManager\Worker\DefaultWorkerQueue;
use PHPExtra\EventManager\Worker\WorkerFactory;
use PHPExtra\EventManager\Worker\WorkerFactoryInterface;
use PHPExtra\EventManager\Worker\WorkerInterface;
use PHPExtra\EventManager\Worker\WorkerQueueInterface;
use PHPExtra\EventManager\Worker\WorkerResult;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The default EventManager implementation
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManager implements EventManagerInterface, LoggerAwareInterface
{
    /**
     * @var WorkerFactoryInterface
     */
    private $workerFactory = null;

    /**
     * @var WorkerQueueInterface|WorkerInterface[]
     */
    private $workerQueue;

    /**
     * Whenever to throw exceptions caught from listeners or not
     *
     * @var bool
     */
    private $throwExceptions = false;

    /**
     * Currently running event
     *
     * @var EventInterface
     */
    private $runningEvent = null;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Create new event manager
     */
    public function __construct()
    {
        $this->workerFactory = new WorkerFactory();
        $this->workerQueue = new DefaultWorkerQueue();
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function trigger(EventInterface $event)
    {
        $workerQueue = clone $this->workerQueue;

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
            $this->logger->info(sprintf('Event %s has no workers', get_class($event)));
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
    private function setRunningEvent(EventInterface $event = null)
    {
        if($event === null){
            $this->logger->debug('Current running event is now NULL');
        }else{
            $this->logger->debug(sprintf('Current running event was set to %s', get_class($event)));
        }

        $this->runningEvent = $event;
    }

    /**
     * @param WorkerInterface $worker
     * @param EventInterface  $event
     *
     * @throws RuntimeException
     * @return WorkerResult
     */
    private function runWorker(WorkerInterface $worker, EventInterface $event)
    {
        $this->logger->debug(sprintf('Starting worker #%s with priority %s for event %s', $worker, $worker->getPriority(), get_class($event)));

        $result = $worker->run($event);
        if (!$result->isSuccessful()) {
            $this->logger->warning(sprintf('Worker #%s failed: %s', $worker, $result->getMessage()));
            if ($this->throwExceptions) {
                $this->logger->debug(sprintf('Throwing exception (throwExceptions is set to true)', $worker));

                $context = new ExceptionContext($event, $worker->getListener());
                $exception = new RuntimeException(sprintf('Worker #%s failed', $worker->getId()), $context, 0, $result->getException());

                throw $exception;
            }
        }

        return $result;
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
        $workers = $this->getWorkerFactory()->createWorkers($listener, $priority);
        $workersCount = 0;

        if ($priority !== null) {
            $this->logger->debug(sprintf('Overriding the priority for all workers to %s in %s', $priority, get_class($listener)));
        }

        foreach ($workers as $worker) {
            $this->addWorker($worker);
            $workersCount++;
        }

        if($workersCount == 0){
            $this->logger->warning(sprintf('Listener "%s" does not have any workers', get_class($listener)));
        }

        return $this;
    }

    /**
     * Get worker factory
     *
     * @return WorkerFactoryInterface
     */
    private function getWorkerFactory()
    {
        return $this->workerFactory;
    }

    /**
     * @param WorkerInterface $worker
     *
     * @return $this
     */
    private function addWorker(WorkerInterface $worker)
    {
        $params = array($worker, $worker->getListenerClass(), $worker->getMethodName(), $worker->getPriority());
        $this->logger->debug(vsprintf('Added new worker (#%s) %s::%s() with priority: %s', $params));

        $this->workerQueue->addWorker($worker);

        return $this;
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