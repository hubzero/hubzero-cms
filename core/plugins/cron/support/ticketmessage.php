<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;

/**
 * Renders a list of support ticket messages
 */
class Ticketmessage extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Ticketmessage';

	/**
	 * Fetch the element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 * @since   1.3.1
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$html = array();

		$html[] = '<select name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '">';

		include_once \Component::path('com_support') . DS . 'models' . DS . 'message.php';
		$messages = \Components\Support\Models\Message::all()->rows();

		$html[] = '<option value="0"' . (!$value ? ' selected="selected"' : '') . '>[ none ]</option>';

		foreach ($messages as $anode)
		{
			$html[] = '<option value="' . $anode->id . '"' . ($value == $anode->id ? ' selected="selected"' : '') . '>' . stripslashes($anode->title) . '</option>';
		}

		$html[] = '</select>';

		return implode("\n", $html);
	}
}
