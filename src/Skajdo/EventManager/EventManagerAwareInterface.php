<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

/**
 * The EventManagerAwareInterface class
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