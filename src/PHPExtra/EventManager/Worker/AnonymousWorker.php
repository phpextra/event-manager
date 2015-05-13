<?php

namespace PHPExtra\EventManager\Worker;

use PHPExtra\EventManager\Event\EventInterface;
use PHPExtra\EventManager\Listener\AnonymousListenerInterface;

class AnonymousWorker extends Worker
{
    /**
     * @param string $id
     * @param AnonymousListenerInterface $listener
     * @param string $eventClass
     * @param int $priority
     */
    public function __construct($id, AnonymousListenerInterface $listener, $eventClass, $priority = null)
    {
        parent::__construct($id, $listener, '__invoke', $eventClass, $priority);
    }

    public function run(EventInterface $event)
    {
        try {
            call_user_func(array($this->getListener()->getClosure(), $this->getMethod()), $event);
            $result = new WorkerResult($this, $event, WorkerResultStatus::SUCCESS);
        } catch (\Exception $e) {
            $result = new WorkerResult($this, $event, WorkerResultStatus::FAILURE, $e);
        }

        return $result;
    }

    /**
     * @return AnonymousListenerInterface
     */
    public function getListener()
    {
        return parent::getListener();
    }


}