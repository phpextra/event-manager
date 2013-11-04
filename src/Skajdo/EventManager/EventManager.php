<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager;

use Skajdo\EventManager\Listener;
use Psr\Log\NullLogger;
use Skajdo\EventManager\Event;
use Skajdo\EventManager\Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Zend\Code\Reflection\ClassReflection;

/**
 * Advanced event manager
 *
 * <p>Example usage:</p>
 * <code>
 * $e = new SomeDummyEvent(); // implements Event
 * $l = new SomeDummyListener(); // implements Listener, has method dummy(SomeDummyEvent $eve)
 * $em = new Manager();
 * $em->addListener($l);
 * $em->triggerEvent($e); // this will run SomeDummyListener::dummy((SomeDummyEvent) obj)
 *
 * <p>You can also define priorities for your listeneres. Default priority is 0</p>
 * <code>
 * /**
 *  * @priority 100
 *  * /
 * public function onSuperEvent(SuperEventExample $event){ ...
 * </code>
 *
 * @author      Jacek Kobus
 */
class EventManager implements LoggerAwareInterface
{
    /**
     * @var Queue
     */
    protected $listenersQueue = array();

    /**
     * Whenever to throw exceptions cought from listeners or not
     * @var bool
     */
    protected $throwExceptions = false;

    /**
     * Currently running event
     *
     * @var Event
     */
    protected $runningEvent = null;

    /**
     * Limit recursion
     *
     * @var int
     */
    protected $recurrencyLimit = 50;

    /**
     * Current recurrency level
     *
     * @var int
     */
    protected $recurrencyLevel = 0;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var CallGraph
     */
    protected $callGraph = null;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->listenersQueue = new Queue();
        if ($logger !== null) {
            $this->setLogger($logger);
        }
    }

    /**
     * @see EventManager::trigger()
     * @deprecated since 1.1.1; use EventManager::trigger() instead
     * @param Event $event
     * @param null $preDispatchHook
     * @param null $postDispatchHook
     * @return EventManager
     */
    public function triggerEvent(Event $event, $preDispatchHook = null, $postDispatchHook = null)
    {
        return $this->trigger($event, $preDispatchHook, $postDispatchHook);
    }

    /**
     * Trigger event; calls all listeners that listen to this event
     *
     * @param  Event $event
     * @param callable $preDispatchHook A callable to be called before dispatching an event to a listener
     * @param callable $postDispatchHook A callable to be called after dispatching an event to a listener
     * @throws \RuntimeException When infinite loop will be detected
     * @throws Exception
     * @return EventManager
     * @return \Skajdo\EventManager\EventManager
     */
    public function trigger(Event $event, $preDispatchHook = null, $postDispatchHook = null)
    {
        $this->runningEvent = $event;
        $eventClassName = get_class($event);
        $listenersFound = 0;
        $loopStart = microtime(true);

        if ($this->callGraph === null) {
            $clearGraph = true;
            $this->callGraph = new CallGraph();
        } else {
            $clearGraph = false;

            if ($this->callGraph->hasObject($event)) {
                $this->getLogger()->critical(
                    sprintf('Recurrency on "%s" was detected and manager will exit', get_class($event))
                );
                throw new \RuntimeException('Recurrency');
            } else {
                $this->callGraph->addObject($event);
            }
        }

        /* @var $queueItem QueueItem */
        foreach ($this->listenersQueue->getIterator() as $listenerId => $queueItem) {

            if (is_a($event, $queueItem->getEventClass())) {

                $listener = $queueItem->getListener();
                $method = $queueItem->getMethod();
                $listenerName = sprintf(
                    '#%s %s::%s(%s $event)',
                    $listenerId,
                    get_class($listener),
                    $method,
                    $eventClassName
                );

                try {

                    if ($preDispatchHook !== null) {
                        call_user_func_array($preDispatchHook, array($listener, $method, $event));
                    }

                    $this->getLogger()->debug(sprintf('Calling %s', $listenerName));
                    $profileStart = microtime(true);
                    call_user_func(array($listener, $method), $event);
                    $profileEnd = bcsub(microtime(true), $profileStart, 8);
                    $this->getLogger()->debug(sprintf('%s took %s sec', $listenerName, $profileEnd));

                    if ($postDispatchHook !== null) {
                        call_user_func_array($postDispatchHook, array($listener, $method, $event));
                    }

                } catch (Exception $e) {

                    $msg = sprintf(
                        'Listener %s threw an exception (%s) with message: "%s"',
                        $listenerName,
                        get_class($e),
                        $e->getMessage()
                    );

                    $ee = new Exception($msg, 0, $e);
                    $ee->setListener($listener);
                    $this->getLogger()->error($msg, array('exception' => $ee));
                    if ($this->getThrowExceptions()) {
                        throw $ee;
                    }
                }
                $listenersFound++;
            }
        }

        if ($clearGraph === true) {
            $this->callGraph = null;
        }

        $loopEnd = bcsub(microtime(true), $loopStart, 8);
        if ($listenersFound == 0) {
            $this->getLogger()->debug(sprintf('%s has no listeners', $eventClassName));
        } else {
            $this->getLogger()->debug(
                sprintf('Event %s was completed for %s listener(s) in %s s', $eventClassName, $listenersFound, $loopEnd)
            );
        }

        $this->runningEvent = null;

        return $this;
    }

    /**
     * Add event listener
     *
     * @see Priority
     * @param Listener|\Closure $listener
     * @param int $priority Optional; overrides other priority settings
     * @throws \InvalidArgumentException If Listener is not an instance of Listener interface nor Closure
     * @return EventManager
     */
    public function addListener($listener, $priority = null)
    {
        // special treatment for closures
        if ($listener instanceof \Closure) {
            $closure = new \ReflectionFunction($listener);
            /* @var $param \Zend\Code\Reflection\ParameterReflection */
            $param = current($closure->getParameters());
            if (!$param || !($eventClassName = $this->_getEventClassName($param))) {
                $this->getLogger()->info(sprintf('Given closure does not listen to any known event'));
            } else {
                $this->_addListener($listener, '__invoke', $eventClassName, $priority);
            }

            return $this;
        }

        if (!$listener instanceof ListenerInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Listener must implement the Listener or it must be an instance of Closure but %s given',
                get_class($listener)
            ));
        }

        $listenerIsListeningToEvent = false;
        $a = new ClassReflection($listenerClass = get_class($listener));
        foreach ($a->getMethods() as $method) {

            /* @var $method \Zend\Code\Reflection\MethodReflection */
            if (($method->getNumberOfParameters() > 1) || !($param = current($method->getParameters()))) {
                continue;
            }

            /* @var $param \Zend\Code\Reflection\ParameterReflection */
            if (($eventClassName = $this->_getEventClassName($param)) === null) {
                continue;
            }

            /**
             * This is a workaround for zend code's bugged getDescription ...
             * At the moment ZF's 2 code library is not good to depend on.
             */
            if ($method->getDocComment() !== false && $priority === null) {
                $matches = array();
                if (preg_match('#@priority ((-?\d+)|\w+)#', $method->getDocComment(), $matches)) {
                    if (count($matches) == 2) {
                        if (is_numeric($matches[1])) {
                            $priority = (int)$matches[1];
                        } else {
                            $priority = Priority::getPriorityByName($matches[1]);
                        }
                    }
                }
            }
            $listenerIsListeningToEvent = true;
            $this->_addListener($listener, $method->getName(), $eventClassName, $priority);
        }

        if (!$listenerIsListeningToEvent) {
            $this->getLogger()->info(
                sprintf('Given Listener (%s) does not listen to any known events', $listenerClass)
            );
        }

        return $this;
    }

    protected function addClosure(\Closure $closure)
    {

    }

    /**
     * Internal method for adding listeners
     *
     * @see Priority
     * @param Listener|\Closure $listener
     * @param $listenerMethodName
     * @param $eventClassName
     * @param $listenerMethodName
     * @param int $priority see Priority (enum)
     * @return EventManager
     */
    protected function _addListener($listener, $listenerMethodName, $eventClassName, $priority = Priority::NORMAL)
    {
        $listenerName = sprintf('%s::%s()', get_class($listener), $listenerMethodName);
        $this->getLogger()->debug(
            sprintf('%s is now listening to %s with priority %s', $listenerName, $eventClassName, $priority)
        );

        $this->listenersQueue->insert(
            new QueueItem($listener, $listenerMethodName, $eventClassName, $priority),
            $priority
        );

        return $this;
    }

    /**
     * Return event class name for given method/function parameter
     * This method will return NULL if the class given in parameter is not
     * an Event nor one of its subclasses.
     *
     * @param \ReflectionParameter $param
     * @return null|string
     */
    protected function _getEventClassName(\ReflectionParameter $param)
    {
        if (!($eventClass = $param->getClass())) {
            return null;
        }

        $eventClassName = $eventClass->getName();
        $requiredInterface = 'Skajdo\EventManager\EventInterface';
        if (!is_subclass_of($eventClassName, $requiredInterface) && $eventClassName != $requiredInterface) {
            return null;
        }

        return $eventClassName;
    }

    /**
     * If this is set to true all exceptions will be thrown
     * and the queue will be interrupted (incomplete).
     *
     * Defaults to FALSE
     *
     * @param $throwExceptions
     * @return EventManager
     */
    public function setThrowExceptions($throwExceptions)
    {
        if ($throwExceptions == true) {
            $this->getLogger()->debug(
                sprintf('%s will now throw exceptions in case of listener failure', get_class($this))
            );
        } else {
            $this->getLogger()->debug(
                sprintf('%s will NOT THROW exceptions in case of listener failure', get_class($this))
            );
        }
        $this->throwExceptions = $throwExceptions;

        return $this;
    }

    /**
     * Return event that is currently running or NULL if no event is running
     *
     * @return null|Event
     */
    public function getRunningEvent()
    {
        return $this->runningEvent;
    }

    /**
     * Tell if current instance of event manager will break the queue
     * if an exception will be thrown from listener.
     *
     * @return boolean
     */
    public function getThrowExceptions()
    {
        return $this->throwExceptions;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return EventManager
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}