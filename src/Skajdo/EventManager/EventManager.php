<?php

namespace Skajdo\EventManager;

use Skajdo\EventManager\Listener;
use Skajdo\EventManager\Listener\ListenerInterface;
use Skajdo\EventManager\Event\EventInterface;
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
     * Array containing listeners with their corresponding methods for each event
     * @var array
     */
    protected $eventTriggers = array();

    /**
     * Whenever to throw exceptions cought from listeners or not
     * @var bool
     */
    protected $throwExceptions = false;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        if($logger !== null){
            $this->setLogger($logger);
        }
    }

    /**
     * Trigger listeners for given event
     *
     * @param  EventInterface $event
     * @throws Exception
     * @return EventManager
     */
    public function triggerEvent(EventInterface $event)
    {
        $eventClassName = get_class($event);
        if (isset($this->eventTriggers[$eventClassName])) {
            $queue = $this->eventTriggers[$eventClassName];

            /* @var Queue $queue */

            foreach ($queue->getIterator() as $id => $data) {
                $object = $data[0];
                $method = $data[1];

                try {
                    call_user_func(array($object, $method), $event);
                } catch (Exception $e) {

                    $msg = sprintf(
                        'Listener (%s#%s) threw an exception (%s) with message: "%s"',
                        get_class($object) . '::' . $method,
                        $id,
                        $e,
                        $e->getMessage()
                    );

                    $ee = new Exception($msg, 0, $e);
                    $ee->setListener($object);

                    if ($this->hasLogger()) {
                        $this->logger->error($msg, array('exception' => $ee));
                    }

                    if ($this->getThrowExceptions()) {
                        throw $ee;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Add event listener
     * @param ListenerInterface|\Closure $listener
     * @param int $priority Optional; overrides other priority settings
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return EventManager
     */
    public function addListener($listener, $priority = null)
    {
        if($listener instanceof \Closure){
            throw new \RuntimeException('Not implemented yet');
        }

        if(!$listener instanceof \Skajdo\EventManager\Listener\Listener){
            throw new \InvalidArgumentException('Listener must implement the ListenerInterface or it must be a Closure');
        }

        $a = new ClassReflection($listenerClass = get_class($listener));
        foreach ($a->getMethods() as $method) {

            /* @var $method \Zend\Code\Reflection\MethodReflection */
            if ($method->getNumberOfParameters() > 1) {
                continue;
            }

            /* @var $param \Zend\Code\Reflection\ParameterReflection */
            $param = current($method->getParameters());

            if (!$param || !($eventClass = $param->getClass())) {
                continue;
            }

            $eventClassName = $eventClass->getName();
            if (!in_array('Skajdo\EventManager\Event\EventInterface', class_implements($eventClassName, false))) {
                continue;
            }

            /**
             * This is a workaround for zend code's bugged getDescription ...
             * At the moment ZF's 2 code library is not good to depend on.
             */
            if ($method->getDocComment() !== false && $priority === null ) {
                $matches = array();
                if (preg_match('#@priority ([\d|\w]+)#', $method->getDocComment(), $matches)) {
                    if (count($matches) == 2) {
                        if(is_numeric($matches[1])){
                            $priority = (int)$matches[1];
                        }else{
                            $priority = Priority::getPriorityByName($matches[1]);
                        }
                    }
                }
            }

            if($priority === null){
                $priority = Priority::NORMAL;
            }

            if (!isset($this->eventTriggers[$eventClassName])) {
                $queue = new Queue();
                $this->eventTriggers[$eventClassName] = $queue;
            } else {
                $queue = $this->eventTriggers[$eventClassName];
            }
            $queue->insert(array($listener, $method->getName()), $priority);
        }

        return $this;
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
        $this->throwExceptions = $throwExceptions;
        return $this;
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
     * @return bool
     */
    protected function hasLogger()
    {
        return $this->logger !== null;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @return EventManager
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}