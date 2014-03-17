<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager;

/**
 * The EventManagerAwareInterface interface
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface EventManagerAwareInterface
{
    /**
     * Set EventManager instance
     *
     * @param EventManager $manager
     * @return $this
     */
    public function setEventManager(EventManager $manager);
}