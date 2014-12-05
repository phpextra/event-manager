<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace PHPExtra\EventManager;

// workaround, see http://php.net/manual/en/reserved.constants.php#88288
defined('PHPEXTRA_EM_PHP_INT_MIN') or define('PHPEXTRA_EM_PHP_INT_MIN', ~PHP_INT_MAX);
defined('PHPEXTRA_EM_PHP_INT_MAX') or define('PHPEXTRA_EM_PHP_INT_MAX', PHP_INT_MAX);

/**
 * The Priority class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
final class Priority
{
    /**
     * Lowest priority
     */
    const LOWEST = -1000;

    /**
     * Lower priority, lower than low.
     */
    const LOWER = -750;

    /**
     * Low priority
     */
    const LOW = -500;

    /**
     * Normal priority used by default
     */
    const NORMAL = 0;

    /**
     * High, above normal
     */
    const HIGH = 500;

    /**
     * Highest priority, above high
     */
    const HIGHER = 750;

    /**
     * Highest priority
     */
    const HIGHEST = 1000;

    /**
     * Special, lowest priority.
     * It should be used to monitor event results.
     * No changes should be made by listener using that priority
     */
    const MONITOR = PHPEXTRA_EM_PHP_INT_MIN;
}