<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager\Listener;

use Closure;
use Skajdo\EventManager\Event\EventInterface;

/**
 * A wrapper for closure listener
 * Uses reflection to obtain information about what event listener is listening to.
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class AnonymousListener implements ListenerInterface
{
    /**
     * @var Closure
     */
    protected $closure;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @param Closure $closure
     * @param int $priority
     * @return AnonymousListener
     */
    function __construct(Closure $closure, $priority = null)
    {
        $this->closure = $closure;
        $this->priority = $priority;
    }

    /**
     * @return Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Invoke an event
     *
     * @param EventInterface $event
     * @return void
     */
    public function invoke(EventInterface $event)
    {
        call_user_func($this->closure, $event);
    }
}