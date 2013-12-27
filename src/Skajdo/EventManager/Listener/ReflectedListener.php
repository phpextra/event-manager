<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Listener;
use Skajdo\EventManager\Priority;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\MethodReflection;

/**
 * Uses reflection to obtain information about what event listener is listening to.
 *
 * @author Jacek Kobus <kobus.jacek@gmail.com>
 */
class ReflectedListener extends AbstractReflectedListener implements NormalizedListenerInterface
{
    /**
     * @var ListenerInterface
     */
    protected $listener;

    /**
     * @var ListenerMethod[]
     */
    protected $methods;

    /**
     * Wrap listener into ReflectedListener to obtain information about events
     *
     * @param ListenerInterface $listener
     */
    function __construct(ListenerInterface $listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return ListenerInterface
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerMethods()
    {
        $methods = array();

        if($this->methods === null){
            $reflectedListener = new ClassReflection($listenerClass = get_class($this->listener));
            foreach ($reflectedListener->getMethods() as $method) {

                /* @var $method \Zend\Code\Reflection\MethodReflection */
                if (($method->getNumberOfParameters() > 1) || !($param = current($method->getParameters()))) {
                    continue;
                }

                /* @var $param \Zend\Code\Reflection\ParameterReflection */
                if (($eventClassName = $this->getEventClassNameFromParam($param)) === null) {
                    continue;
                }

                $priority = $this->getPriority($method);
                $methods[] = new ListenerMethod($this->getListener(), $method->getName(), $eventClassName, $priority);
            }
        }else{
            $methods = $this->methods;
        }
        return $methods;
    }

    /**
     * Try to find a priority for given method
     *
     * @param MethodReflection $method
     * @return int|null
     */
    protected function getPriority(MethodReflection $method)
    {
        $priority = null;
        if($method->getDocBlock() !== false){
            /** @var $tag \Zend\Code\Reflection\DocBlock\Tag\GenericTag */
            $tag = $method->getDocBlock()->getTag('priority');

            if($tag !== false){
                if(is_numeric($tag->getContent())){
                    $priority = (int)$tag->getContent();
                }else{
                    $priority = Priority::getPriorityByName($tag->getContent());
                }
            }
        }
        return $priority;
    }
}