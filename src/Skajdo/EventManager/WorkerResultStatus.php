<?php

namespace Skajdo\EventManager;

/**
 * Worker result status
 * If status is > 0 then an error occurred
 */
interface WorkerResultStatus
{
    const SUCCESS = 0;
    const FAILURE = 1;
}