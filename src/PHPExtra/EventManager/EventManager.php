<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Exception\ListenerException;
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
     * @var ListenerException[]
     */
    private $exceptions = array();

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
        if($this->runningEvent === null){
            $this->clearExceptions();
        }

        $previousRunningEvent = $this->runningEvent;
        $this->runningEvent = $event;

        $workers = $this->workerQueue->getWorkersFor($event);

        foreach ($workers as $worker) {
            $result = $this->runWorker($worker, $event);

            if (!$result->isSuccessful()) {

                $message = sprintf('Listener "%s" failed: "%s"', $result->getWorker()->getName(), $result->getMessage());

                $this->logger->warning($message);
                $exception = new ListenerException($event, $worker->getListener(), $message, $result->getException());
                $this->addException($exception);

                if ($this->throwExceptions) {
                    $this->logger->debug(sprintf('Throwing exception (throwExceptions is set to true)', $worker));
                    throw $exception;
                }
            }
        }

        $this->runningEvent = $previousRunningEvent;
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
     * @throws ListenerException
     * @return WorkerResult
     */
    private function runWorker(Worker $worker, Event $event)
    {
        $this->logger->debug(sprintf('Starting %s', $worker->getName()));

        $this->onWorkerStart($worker, $event);
        $result = $worker->run($event);
        $this->onWorkerStop($worker, $event);

        return $result;
    }

    /**
     * Get all exceptions that occurred during emitting an event.
     *
     * @return ListenerException[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Tell if the manager has collected any exceptions after last emit call.
     *
     * @return bool
     */
    public function hasExceptions()
    {
        return !empty($this->exceptions);
    }

    /**
     * @param ListenerException $exception
     */
    private function addException(ListenerException $exception)
    {
        $this->exceptions[] = $exception;
    }

    /**
     * Clear all collected exceptions
     */
    private function clearExceptions()
    {
        $this->exceptions = array();
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