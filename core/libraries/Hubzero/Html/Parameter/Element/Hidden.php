<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder\Input;

/**
 * Renders a hidden element
 */
class Hidden extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Hidden';

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
		$class = (string) $node['class'];
		$class = $class ?: 'text_area';

		return Input::hidden($name, $value, array('class' => $class));
	}

	/**
	 * Fetch tooltip for a hidden element
	 *
	 * @param   string  $label         Element label
	 * @param   string  $description   Element description (which renders as a tool tip)
	 * @param   object  &$xmlElement   Element object
	 * @param   string  $control_name  Control name
	 * @param   string  $name          Element name
	 * @return  string
	 */
	public function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
	{
		return false;
	}
}
