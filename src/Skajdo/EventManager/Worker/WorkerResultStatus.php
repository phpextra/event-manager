<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager\Worker;

/**
 * Worker result status
 * If status is > 0 then an error occurred
 *
 * @see WorkerResultStatus::SUCCESS
 * @see WorkerResultStatus::FAILURE
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
final class WorkerResultStatus
{
    const SUCCESS = 0;
    const FAILURE = 1;
}