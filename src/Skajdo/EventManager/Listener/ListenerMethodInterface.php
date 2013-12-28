<?php
/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
namespace Skajdo\EventManager\Listener;


/**
 * Each listener can have many method to event pairs that are represented by this object.
 * Each pair can have different priority.
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
interface ListenerMethodInterface
{
    /**
     * @return string
     */
    public function getEventClassName();

    /**
     * @return ListenerInterface
     */
    public function getListener();

    /**
     * @return string
     */
    public function getMethodName();

    /**
     * @return int
     */
    public function getPriority();
}