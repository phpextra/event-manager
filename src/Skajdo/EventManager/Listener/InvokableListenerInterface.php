<?php

namespace Skajdo\EventManager\Listener;

use Skajdo\EventManager\EventInterface;
use Skajdo\EventManager\Listener\ListenerInterface;

interface InvokableListenerInterface extends ListenerInterface
{
    /**
     * Invoke an event
     *
     * @param EventInterface $event
     * @return void
     */
    public function invoke(EventInterface $event);
}