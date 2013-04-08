<?php

namespace Skajdo\EventManager;
use Skajdo\EventManager\Listener\ListenerInterface;

/**
 * Queue item.
 *
 * @author Jacek Kobus
 */
class QueueItem
{
    /**
     * @var ListenerInterface|\Closure
     */
    protected $listener;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $eventClass;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @param ListenerInterface|\Closure $listener
     * @param string $method
     * @param string $eventClass
     * @param int $priority
     * @throws \InvalidArgumentException If Listener is not an instance of Listener interface nor Closure
     */
    public function __construct($listener, $method, $eventClass, $priority = Priority::NORMAL)
    {
//        if((!$listener instanceof ListenerInterface) && (!$listener instanceof \Closure)){
//            throw new \InvalidArgumentException(sprintf('Listener must implement the ListenerInterface or it must be an instance of Closure but %s given', get_class($listener)));
//        }

        $this->listener = $listener;
        $this->method = $method;
        $this->eventClass = $eventClass;
        $this->priority = $priority;
    }

    /**
     * @param string $eventClass
     */
    public function setEventClass($eventClass)
    {
        $this->eventClass = $eventClass;
    }

    /**
     * @return string
     */
    public function getEventClass()
    {
        return $this->eventClass;
    }

    /**
     * @param \Skajdo\EventManager\Listener\ListenerInterface $listener
     */
    public function setListener($listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return \Skajdo\EventManager\Listener\ListenerInterface|\Closure
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

}