<?php

namespace Skajdo\EventManager\Listener;

use Closure;
use Skajdo\EventManager\EventInterface;
use Skajdo\EventManager\Priority;

/**
 * A wrapper for closure listener
 * Uses reflection to obtain information about what event listener is listening to.
 */
class AnonymousListener implements NormalizedListenerInterface, InvokableListenerInterface
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
    function __construct(Closure $closure, $priority = Priority::NORMAL)
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
        $requiredInterface = 'Skajdo\EventManager\EventInterface';

        if($param){
            if (!($eventClass = $param->getClass())) {
                throw new \RuntimeException('Closure does not contain a reference to an event');
            }

            $eventClassName = $eventClass->getName();
            if (!is_subclass_of($eventClassName, $requiredInterface) && $eventClassName != $requiredInterface) {
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