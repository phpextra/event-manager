<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.md for copying permission.
 */

namespace Skajdo\EventManager\Listener;

use Closure;
use Skajdo\EventManager\Event\EventInterface;

/**
 * A wrapper for closure listener
 * Uses reflection to obtain information about what event listener is listening to.
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class AnonymousListener extends AbstractReflectedListener implements NormalizedListenerInterface, InvokableListenerInterface
{
    /**
     * @var Closure
     */
    protected $closure;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @param Closure $closure
     * @param int $priority
     * @return AnonymousListener
     */
    function __construct(Closure $closure, $priority = null)
    {
        $this->closure = $closure;
        $this->priority = $priority;
    }

    /**
     * @return Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Invoke an event
     *
     * @param EventInterface $event
     * @return void
     */
    public function invoke(EventInterface $event)
    {
        call_user_func($this->closure, $event);
    }

    /**
     * Return event class name paired with method that should be called for that event
     *
     * @throws \InvalidArgumentException
     * @return ListenerMethod[]
     */
    public function getListenerMethods()
    {
        $closureReflection = new \ReflectionFunction($this->getClosure());

        $params = $closureReflection->getParameters();
        if(isset($params[0])){
            $param = $params[0];

            if (($eventClassName = $this->getEventClassNameFromParam($param)) === null) {
                throw new \InvalidArgumentException(sprintf('First closure param (%s) must be a class implementing the EventInterface', $param->getName()));
            }
            return array(new ListenerMethod($this, 'invoke', $eventClassName, $this->priority));
        }
        return array();
    }

    /**
     * Static factory for closures
     *
     * @param Closure $closure
     * @return AnonymousListener
     */
    public static function create(Closure $closure)
    {
        return new self($closure);
    }
}