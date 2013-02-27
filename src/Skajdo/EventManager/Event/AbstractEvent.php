<?php

namespace Skajdo\EventManager\Event;

/**
 * Represents an event
 *
 * @author      Jacek Kobus
 * @category    App
 * @package     App_EventManager
 */
class AbstractEvent implements EventInterface
{
	/**
	 * Tell if current event is cancellable
	 * @return bool
	 */
	final public function isCancellable()
	{
		return ($this instanceof CancellableEvent);
	}
}