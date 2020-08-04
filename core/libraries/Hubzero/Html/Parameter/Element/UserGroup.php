<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder\Access;

/**
 * Renders a editors element
 */
class UserGroup extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'UserGroup';

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
		$ctrl = $control_name . '[' . $name . ']';
		$attribs = ' ';

		if ($v = $node['size'])
		{
			$attribs .= 'size="' . (string) $v . '"';
		}
		if ($v = $node['class'])
		{
			$attribs .= 'class="' . (string) $v . '"';
		}
		else
		{
			$attribs .= 'class="inputbox"';
		}
		if ($m = $node['multiple'])
		{
			$attribs .= 'multiple="multiple"';
			$ctrl .= '[]';
		}

		return Access::usergroup($ctrl, $value, $attribs, false);
	}
}
