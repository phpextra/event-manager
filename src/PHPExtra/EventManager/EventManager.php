<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Exception\EventException;
use PHPExtra\EventManager\Listener\Listener;
use PHPExtra\EventManager\Worker\ArrayWorkerQueue;
use PHPExtra\EventManager\Worker\WorkerQueue;
use PHPExtra\EventManager\Worker\Worker;
use PHPExtra\EventManager\Worker\WorkerFactory;
use PHPExtra\EventManager\Worker\WorkerResult;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The default EventManager implementation
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class EventManager implements EventEmitter, LoggerAwareInterface
{
    /**
     * @var WorkerFactory
     */
    private $workerFactory = null;

    /**
     * @var WorkerQueue|Worker[]
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
     * @var Event
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
        $this->workerQueue = new ArrayWorkerQueue();
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function emit(Event $event)
    {
        $previousRunningEvent = $this->runningEvent;
        $this->runningEvent = $event;

        $workers = $this->workerQueue->getWorkersFor($event);

        if (count($workers) > 0) {
            foreach ($workers as $worker) {
                $this->runWorker($worker, $event);
            }
        } else {
            $this->logger->debug(sprintf('Event %s has no workers', get_class($event)));
        }

        $this->runningEvent = $previousRunningEvent;

        return $this;
    }

    /**
     * @return Event
     */
    public function getRunningEvent()
    {
        return $this->runningEvent;
    }

    /**
     * @param Worker $worker
     * @param Event  $event
     */
    protected function onWorkerStart(Worker $worker, Event $event)
    {
    }

    /**
     * @param Worker $worker
     * @param Event  $event
     */
    protected function onWorkerStop(Worker $worker, Event $event)
    {
    }

    /**
     * @param Worker $worker
     * @param Event  $event
     *
     * @throws EventException
     * @return WorkerResult
     */
    private function runWorker(Worker $worker, Event $event)
    {
        $this->logger->debug(sprintf('Starting worker #%s with priority %s for event %s', $worker, $worker->getPriority(), get_class($event)));

        $this->onWorkerStart($worker, $event);
        $result = $worker->run($event);
        $this->onWorkerStop($worker, $event);

        if (!$result->isSuccessful()) {
            $this->logger->warning(sprintf('Worker #%s failed: %s', $worker, $result->getMessage()));

            if ($this->throwExceptions) {
                $this->logger->debug(sprintf('Throwing exception (throwExceptions is set to true)', $worker));
                $exception = new EventException($event, $worker->getListener(), sprintf('Worker #%s failed', $worker->getId()), $result->getException());

                throw $exception;
            }
        }

        return $result;
    }

    /**
     * Set to true to throw exceptions coming from listeners.
     * By default all exceptions are suppressed.
     *
     * @param bool $throwExceptions
     *
     * @return $this
     */
    public function setThrowExceptions($throwExceptions = true)
    {
        $this->throwExceptions = (bool)$throwExceptions;

        return $this;
    }

    /**
     * @see Priority
     *
     * @param Listener $listener
     * @param int      $priority
     *
     * @return $this
     */
    public function add(Listener $listener, $priority = null)
    {
        $workers = $this->workerFactory->createWorkers($listener, $priority);
        $workersCount = 0;

        if ($priority !== null) {
            $this->logger->debug(sprintf('Overriding priority for all workers to %s in %s', $priority, get_class($listener)));
        }

        foreach ($workers as $worker) {
            $this->addWorker($worker);
            $workersCount++;
        }

        if ($workersCount == 0) {
            $this->logger->debug(sprintf('Listener "%s" does not have any workers', get_class($listener)));
        }

        return $this;
    }

    /**
     * @param Worker $worker
     *
     * @return $this
     */
    private function addWorker(Worker $worker)
    {
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