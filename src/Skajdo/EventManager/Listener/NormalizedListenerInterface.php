<?php

namespace Skajdo\EventManager\Listener;

use Skajdo\EventManager\Listener\ListenerInterface;

/**
 * Normalized listener that can return us a list of method to event pairs along with priority for each pair
 */
interface NormalizedListenerInterface extends ListenerInterface
{
    /**
     * Return event class name paired with method that should be called for that event
     * Each method is aware of its listener
     *
     * @return ListenerMethod[]
     */
    public function getListenerMethods();
}