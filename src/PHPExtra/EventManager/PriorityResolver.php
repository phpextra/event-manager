<?php

/**
 * Copyright (c) 2014 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager;

/**
 * The PriorityResolver class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
final class PriorityResolver
{
    /**
     * Name to int mapping
     *
     * @var array
     */
    private static $nameToPriority = array(
        'lowest'    => Priority::LOWEST,
        'lower'     => Priority::LOWER,
        'low'       => Priority::LOW,
        'normal'    => Priority::NORMAL,
        'high'      => Priority::HIGH,
        'higher'    => Priority::HIGHER,
        'highest'   => Priority::HIGHEST,
        'monitor'   => Priority::MONITOR,
    );

    /**
     * Get human readable priority name
     *
     * @param int $priority
     *
     * @return string
     * @throws \InvalidArgumentException If unable to find priority name
     */
    public static function getPriorityName($priority)
    {
        $priorityToName = array_flip(static::$nameToPriority);
        if (!isset($priorityToName[$priority])) {
            throw new \InvalidArgumentException(sprintf('Unknown priority given: "%s"', $priority));
        }

        return $priorityToName[$priority];
    }

    /**
     * Get priority integer value by priority name
     *
     * @param string $priorityName
     *
     * @return int
     * @throws \InvalidArgumentException If name is invalid
     */
    public static function getPriorityByName($priorityName)
    {
        $priorityName = strtolower($priorityName);
        if (!isset(static::$nameToPriority[$priorityName])) {
            throw new \InvalidArgumentException(sprintf('Unknown priority name given: "%s"', $priorityName));
        }

        return static::$nameToPriority[$priorityName];
    }

    /**
     * Get priority from method comment or null if priority was not found in given string
     *
     * @param string $comment
     * @param int $default
     *
     * @return int|null
     */
    public static function getPriorityFromDocComment($comment, $default = null)
    {
        $priority = null;
        $pattern = '#@priority\\s+(\-?\d+|\w+)#i';

        $matches = array();
        preg_match($pattern, $comment, $matches);

        if (isset($matches[1])) {
            if (is_numeric($matches[1])) {
                $priority = (int)$matches[1];
            } else {
                $priority = static::getPriorityByName($matches[1]);
            }
        }

        return $priority === null ? $default : $priority;
    }
} 