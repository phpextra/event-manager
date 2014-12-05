<?php

/**
 * Copyright (c) 2016 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */
 
namespace PHPExtra\EventManager;

/**
 * The PriorityResolver class
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class PriorityResolver
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
     *
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

    /**
     * Get priority from method comment or null if priority was not found in given string
     *
     * @param string $comment
     *
     * @return int|null
     */
    public static function getPriorityFromDocComment($comment)
    {
        $priority = null;
        $pattern = '#@priority\\s+(\-?\d+|\w+)#i';

        $matches = array();
        preg_match($pattern, $comment, $matches);

        if (isset($matches[1])) {
            if (is_numeric($matches[1])) {
                $priority = (int)$matches[1];
            } else {
                $priority = self::getPriorityByName($matches[1]);
            }
        }

        return $priority;
    }
} 