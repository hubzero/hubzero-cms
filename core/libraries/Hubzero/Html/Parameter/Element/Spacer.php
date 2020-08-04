<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;

/**
 * Renders a spacer element
 */
class Spacer extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Spacer';

	/**
	 * Fetch tooltip for a radio button
	 *
	 * @param   string  $label         Element label
	 * @param   string  $description   Element description for tool tip
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @param   string  $name          The name.
	 * @return  string
	 */
	public function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		return '&#160;';
	}

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
		if ($value)
		{
			return \App::get('language')->txt($value);
		}

		return ' ';
	}
}
