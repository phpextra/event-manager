<?php

namespace Skajdo\EventManager\Event;

/**
 * Represents cancellable event
 *
 * @author      Jacek Kobus
 * @category    App
 * @package     App_EventManager
 */
interface CancellableEvent
{
	/**
	 * Tell if current event is cancelled
	 * @return bool
	 */
	public function isCancelled();

	/**
	 * Tell if current event is cancelled
	 * @return bool
	 */
	public function getIsCancelled();
}