<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

use Psr\Log\LoggerInterface;

/**
 * The AbstractWorkerQueue class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
abstract class AbstractWorkerQueue implements WorkerQueueInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->count() == 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->getWorkers());
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->getWorkers();
    }
}