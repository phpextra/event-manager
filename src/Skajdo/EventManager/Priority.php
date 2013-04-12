<?php

namespace Skajdo\EventManager;

/**
 * Event priority ENUM class
 */
final class Priority
{
    const LOWEST    = -1000;
    const LOW       = -500;
    const NORMAL    = 0;
    const HIGH      = 500;
    const HIGHEST   = 1000;

    /**
     * Name to int mapping
     *
     * @var array
     */
    protected static $nameToPriority = array(
        'lowest'    => self::LOWEST,
        'low'       => self::LOW,
        'normal'    => self::NORMAL,
        'high'      => self::HIGH,
        'highest'   => self::HIGHEST,
    );

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
        if(!isset(self::$nameToPriority[$priorityName])){
            throw new \InvalidArgumentException(sprintf('Unknown priority name given: "%s"', $priorityName));
        }
        return self::$nameToPriority[$priorityName];
    }
}