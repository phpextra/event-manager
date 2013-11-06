<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

/**
 * Event priority ENUM class
 */
final class Priority
{
    const LOWEST = -1000;
    const LOW = -500;
    const NORMAL = 0;
    const HIGH = 500;
    const HIGHEST = 1000;

    /**
     * Special priority (lower than lowest);
     * It should be used to monitor event results.
     * No changes should be made in that event.
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
     * @param $priority
     * @return string
     * @throws \InvalidArgumentException
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