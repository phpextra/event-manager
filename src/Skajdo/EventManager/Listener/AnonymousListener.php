<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Listener;

use Closure;
use Skajdo\EventManager\EventInterface;

/**
 * A wrapper for closure listener
 * Uses reflection to obtain information about what event listener is listening to.
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
     * @param $priority
     * @return \Skajdo\EventManager\Listener\AnonymousListener
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
     * @throws \RuntimeException If closure param is missing or its not an event
     * @return ListenerMethod[]
     */
    public function getListenerMethods()
    {
        $closureReflection = new \ReflectionFunction($this->getClosure());

        /* @var $param \Zend\Code\Reflection\ParameterReflection */
        $param = current($closureReflection->getParameters());

        if($param){
            if (($eventClassName = $this->getEventClassNameFromParam($param)) === null) {
                throw new \RuntimeException('Closure param must be an event');
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