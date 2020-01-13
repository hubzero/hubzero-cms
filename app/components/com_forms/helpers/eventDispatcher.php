<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

use Hubzero\Utility\Arr;

class EventDispatcher
{

	/**
	 * Returns an EventDispatcher instance
	 *
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_dispatcher = Arr::getValue(
			$args, 'dispatcher', new MockProxy(['class' => 'Event'])
		);
		$this->_baseScope = Arr::getValue($args, 'scope', 'forms');
	}

	/**
	 *
	 * @param    string   $eventDescription   Event description
	 * @param    array    $eventData          Event data
	 * @return   void
	 */
	public function dispatch($eventDescription, $eventData)
	{
		$eventName = $this->_generateEventName($eventDescription);

		$this->_triggerEvent($eventName, $eventData);
	}

	/**
	 * Generates event name
	 *
	 * @param    string   $action   Action taken
	 * @return   string
	 */
	protected function _generateEventName($action)
	{
		$eventName = "$this->_baseScope.$action";

		return $eventName;
	}

	/**
	 * Triggers given event with the given data
	 *
	 * @param    string   $eventName   Name of the event
	 * @param    array    $args        Arguments to the event handler
	 * @return   void
	 */
	protected function _triggerEvent($eventName, $args)
	{
		$this->_dispatcher->trigger($eventName, $args);
	}

}
