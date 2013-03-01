<?php

namespace Skajdo\EventManager\Listener;

use Skajdo\EventManager\Event\EventInterface;

/**
 * Abstract listener class
 *
 * @author      Jacek Kobus
 */
abstract class AbstractListener implements ListenerInterface
{
    /**
     * Call listener
     * @param \Skajdo\EventManager\Event\EventInterface $event
     * @return void
     */
    public function run(EventInterface $event)
    {
    }
}