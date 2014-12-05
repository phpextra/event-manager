<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Listener;

use Closure;

/**
 * A wrapper for closure listener
 * Uses reflection to obtain information about what event listener is listening to.
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class AnonymousListener implements ListenerInterface, AnonymousListenerInterface
{
    /**
     * @var Closure
     */
    private $closure;

    /**
     * @var int
     */
    private $priority;

    /**
     * @param Closure $closure
     * @param int     $priority
     *
     * @return AnonymousListener
     */
    function __construct(Closure $closure, $priority = null)
    {
        $this->closure = $closure;
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }
}