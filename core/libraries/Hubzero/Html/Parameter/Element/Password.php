<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;

/**
 * Renders a password element
 */
class Password extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Password';

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
		$size = (string) $node['size'];
		$size = ($size ? 'size="' . $size . '"' : '');

		$class = (string) $node['class'];
		$class = ($class ? 'class="' . $class . '"' : 'class="text_area"');

		return '<input type="password" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $value . '" ' . $class . ' ' . $size . ' />';
	}
}
