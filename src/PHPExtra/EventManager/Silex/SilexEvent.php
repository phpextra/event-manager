<?php

namespace PHPExtra\EventManager\Silex;

use PHPExtra\EventManager\Event\CancellableEvent;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * The SilexEvent class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class SilexEvent extends CancellableEvent
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var SymfonyEvent
     */
    protected $event;

    /**
     * @param SymfonyEvent $event Symfony event instance
     * @param string       $name Symfony event name
     */
    function __construct($name, SymfonyEvent $event)
    {
        $this->event = $event;
        $this->name = $name;
    }

    /**
     * Returns symfony's event.
     *
     * @return SymfonyEvent
     */
    public function getSymfonyEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function isCancelled()
    {
        return $this->getSymfonyEvent()->isPropagationStopped() || parent::isCancelled();
    }

    public function cancel($reason = null)
    {
        $this->event->stopPropagation();
        parent::cancel($reason);
    }
}