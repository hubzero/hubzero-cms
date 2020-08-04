<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder\Behavior;
use Hubzero\Html\Builder\Input;

/**
 * Renders a calendar element
 */
class Calendar extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Calendar';

	/**
	 * Fetch a calendar element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		// Load the calendar behavior
		Behavior::calendar();

		$format = $node->attributes('format') ? $node->attributes('format') : '%Y-%m-%d';
		$class  = $node->attributes('class')  ? $node->attributes('class')  : 'inputbox';

		return Input::calendar($name, $value, array(
			'format' => $format,
			'class'  => $class
		));
	}
}
