<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager\Listener;

use Closure;
use PHPExtra\EventManager\Event\Event;
use PHPExtra\EventManager\Priority;

/**
 * A wrapper for closure listener
 * Uses reflection to obtain information about what event listener is listening to.
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class AnonymousListener implements Listener
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
     */
    public function __construct(Closure $closure, $priority = Priority::NORMAL)
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

    /**
     * {@inheritdoc}
     */
    public function invoke(Event $event)
    {
        return call_user_func(array($this->closure, '__invoke'), $event);
    }
}