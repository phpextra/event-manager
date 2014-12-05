<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager\Silex;

use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\EventManager;
use PHPExtra\EventManager\Worker\Worker;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * The ProfilableEventManager class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class ProfilableEventManager extends EventManager
{
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * The ProfilableEventManager constructor.
     *
     * @param Stopwatch $stopwatch Optional; can be set later
     */
    public function __construct(Stopwatch $stopwatch = null)
    {
        parent::__construct();

        if(!$stopwatch){
            $stopwatch = new NullStopwatch();
        }

        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function emit(Event $event)
    {
        $name = $this->getEventName($event);
        $this->stopwatch->start($name);
        parent::emit($event);
        $this->stopwatch->stop($name);
    }

    /**
     * @param Worker $worker
     * @param Event  $event
     */
    protected function onWorkerStart(Worker $worker, Event $event)
    {
        $this->stopwatch->start($this->getWorkerName($worker));
        parent::onWorkerStart($worker, $event);
    }

    /**
     * @param Worker $worker
     * @param Event  $event
     */
    protected function onWorkerStop(Worker $worker, Event $event)
    {
        $this->stopwatch->stop($this->getWorkerName($worker));
        parent::onWorkerStop($worker, $event);
    }

    private function getEventName(Event $event)
    {
        return get_class($event);
    }

    /**
     * @param Worker $worker
     *
     * @return string
     */
    private function getWorkerName(Worker $worker)
    {
        return sprintf('%s::%s', $worker->getListenerClass(), $worker->getMethodName());
    }

    /**
     * @param Stopwatch $stopwatch
     *
     * @return $this
     */
    public function setStopwatch(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;

        return $this;
    }
}