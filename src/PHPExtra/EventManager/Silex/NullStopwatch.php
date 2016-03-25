<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager\Silex;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * The NullStopwatch class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class NullStopwatch extends Stopwatch
{
    public function start($name, $category = null)
    {
    }

    public function stop($name)
    {
    }
}