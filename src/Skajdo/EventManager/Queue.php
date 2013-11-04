<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

use Zend\Stdlib\PriorityQueue;

/**
 * Priority queue.
 *
 * Fixes issues with default Queue. Iterations may be slow as inner iterator is created.
 * It was made due to bug-feature (?) that causes queue elements to be removed.
 *
 * If using Zend, it has its own SplPriorityQueue replacement if PHP < 5.3
 * as it's available only in 5.3 and above.
 *
 * @author      Jacek Kobus
 */
class Queue extends PriorityQueue
{
}