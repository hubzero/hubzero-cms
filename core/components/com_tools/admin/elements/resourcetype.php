<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;

/**
 * Renders a list of resource types
 */
class Resourcetype extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Resourcetype';

	/**
	 * Fetch the element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		include_once \Component::path('com_resources') . DS . 'models' . DS . 'type.php';

		$types = \Components\Resources\Models\Type::getMajorTypes();

		$html = array();
		$html[] = '<select name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '">';
		$html[] = '<option value="0"' . ($value === 0 || $value === '0' ? ' selected="selected"' : '') . '>Select type</option>';

		foreach ($types as $type)
		{
			$html[] = '<option value="' . $type->id . '"' . ($value == $type->id ? ' selected="selected"' : '') . '>' . stripslashes($anode->type) . '</option>';
		}

		$html[] = '</select>';

		return implode("\n", $html);
	}
}
