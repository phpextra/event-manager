<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Listener;

use PHPExtra\EventManager\Event\EventInterface;

/**
 * The InvokableListener interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface AnonymousListenerInterface extends ListenerInterface
{
    /**
     * @return int
     */
    public function getPriority();

    /**
     * @return \Closure
     */
    public function getClosure();

    /**
     * @param EventInterface $event
     *
     * @return void
     */
    public function invoke(EventInterface $event);
}