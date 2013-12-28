<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager;

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
     * Highest priority
     */
    const HIGHEST = 1000;

    /**
     * Special priority (one-million, lower than lowest);
     * It should be used to monitor event results.
     * No changes should be made by listener using that priority
     */
    const MONITOR = -1000000;

    /**
     * Name to int mapping
     *
     * @var array
     */
    protected static $nameToPriority = array(
        'lowest' => self::LOWEST,
        'low' => self::LOW,
        'normal' => self::NORMAL,
        'high' => self::HIGH,
        'highest' => self::HIGHEST,
        'monitor' => self::MONITOR,
    );

    /**
     * Get human readable priority name
     *
     * @param int $priority
     * @return string
     * @throws \InvalidArgumentException If unable to find priority name
     */
    public static function getPriorityName($priority)
    {
        $priorityToName = array_flip(self::$nameToPriority);
        if (!isset($priorityToName[$priority])) {
            throw new \InvalidArgumentException(sprintf('Unknown priority given: "%s"', $priority));
        }

        return $priorityToName[$priority];
    }

    /**
     * Get priority integer value by priority name
     *
     * @param string $priorityName
     * @return int
     * @throws \InvalidArgumentException If name is invalid
     */
    public static function getPriorityByName($priorityName)
    {
        $priorityName = strtolower($priorityName);
        if (!isset(self::$nameToPriority[$priorityName])) {
            throw new \InvalidArgumentException(sprintf('Unknown priority name given: "%s"', $priorityName));
        }

        return self::$nameToPriority[$priorityName];
    }
}