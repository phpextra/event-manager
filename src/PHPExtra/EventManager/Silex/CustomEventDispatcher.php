<?php

namespace PHPExtra\EventManager\Silex;

use PHPExtra\EventManager\EventManager;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Triggers symfony events on EventManager using SilexEvent class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class CustomEventDispatcher extends EventDispatcher
{
    /**
     * @var EventManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, SymfonyEvent $event = null)
    {
        parent::dispatch($eventName, $event);

        if (null === $event) {
            $event = new SymfonyEvent();
        }

        $this->em->emit(new SilexEvent($eventName, $event));

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventManager(EventManager $em)
    {
        $this->em = $em;

        return $this;
    }
}