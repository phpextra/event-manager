<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

/**
 * Collect called objects and count them
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class CallGraph implements \Countable
{
    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var array
     */
    protected $calls = array();

    /**
     * @param mixed $object
     * @return $this
     */
    public function addObject($object)
    {
        $this->count++;
        $this->calls[] = $object;

        return $this;
    }

    /**
     * @param mixed $object
     * @return bool
     */
    public function hasObject($object)
    {
        return in_array($object, $this->calls);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->count = 0;
        $this->calls = array();

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }
}