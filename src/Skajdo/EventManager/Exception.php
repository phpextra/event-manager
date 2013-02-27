<?php

namespace Skajdo\EventManager;
use Skajdo\EventManager\Listener\ListenerInterface;

/**
 * Event manager exception
 *
 * @author      Jacek Kobus
 * @category    App
 * @package     App_EventManager
 */
class Exception extends \Exception
{
	/**
	 * @var ListenerInterface
	 */
	protected $listener;

	/**
	 * @param ListenerInterface $listener
	 * @return Exception
	 */
	public function setListener($listener)
	{
		$this->listener = $listener;
		return $this;
	}

	/**
	 * @return ListenerInterface
	 */
	public function getListener()
	{
		return $this->listener;
	}
}