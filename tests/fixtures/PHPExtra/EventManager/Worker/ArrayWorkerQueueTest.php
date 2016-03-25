<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace PHPExtra\EventManager\Worker;

class ArrayWorkerQueueTest extends WorkerQueueTest
{
    protected function setUp()
    {
        $this->queue = new ArrayWorkerQueue();
    }
}
