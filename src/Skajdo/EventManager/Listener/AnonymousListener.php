<?php

namespace Skajdo\EventManager\Listener;

use Closure;
use Skajdo\EventManager\EventInterface;
use Skajdo\EventManager\ListenerInterface;
use Skajdo\EventManager\Priority;

class AnonymousListener implements ListenerInterface
{
    /**
     * @var Closure
     */
    protected $closure;

    /**
     * @param Closure $closure
     * @param int $priority
     */
    function __construct(Closure $closure, $priority = Priority::NORMAL)
    {
        $this->closure = $closure;
    }

    public function getEventClassName()
    {

    }

    public function getMethodName()
    {
        return '__invoke';
    }

    public function getPriority()
    {

    }

    /**
     * @param EventInterface $event
     */
    public function call(EventInterface $event)
    {
        call_user_func($this->closure);
    }

    public function setPriority($priority)
    {
    }
}