<?php

/**
 * Copyright (c) 2013 Jacek Kobus <kobus.jacek@gmail.com>
 * See the file LICENSE.txt for copying permission.
 */

namespace Skajdo\EventManager\Listener;

use Skajdo\EventManager\Priority;
use Zend\Code\Reflection\ClassReflection;

/**
 * A wrapper for a basic listener that is created using reflection
 * Uses reflection to obtain information about what event listener is listening to.
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
     * @return \Skajdo\EventManager\Listener\ListenerInterface
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
                $priority = null;
                /* @var $method \Zend\Code\Reflection\MethodReflection */
                if (($method->getNumberOfParameters() > 1) || !($param = current($method->getParameters()))) {
                    continue;
                }

                /* @var $param \Zend\Code\Reflection\ParameterReflection */
                if (($eventClassName = $this->getEventClassNameFromParam($param)) === null) {
                    continue;
                }

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
                $methods[] = new ListenerMethod($this->getListener(), $method->getName(), $eventClassName, $priority);
            }
        }else{
            $methods = $this->methods;
        }
        return $methods;
    }
}