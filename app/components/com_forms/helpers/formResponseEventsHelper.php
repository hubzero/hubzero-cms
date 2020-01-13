<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Helpers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/eventDispatcher.php";

use Components\Forms\Helpers\EventDispatcher;
use Hubzero\Utility\Arr;

class FormResponseEventsHelper extends ComponentRouter
{

	/**
	 * Returns FormResponseEventsHelper instance
	 *
	 * @return   void
	 */
	public function __construct($args = [])
	{
		$this->_dispatcher = Arr::getValue(
			$args, 'dispatcher', new EventDispatcher()
		);
	}

	/**
	 * Triggers the field response update event
	 *
	 * @param    array   $fieldResponses   User field responses
	 * @return   void
	 */
	public function fieldResponsesUpdate($fieldResponses)
	{
		$this->_dispatcher->dispatch('onFieldResponsesUpdate', [$fieldResponses]);
	}

}
