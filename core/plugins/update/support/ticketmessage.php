<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Renders a list of support ticket messages
 */
class Ticketmessage extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Ticketmessage';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$html = array();

		$html[] = '<select name="' . $this->name . '" id="' . $this->id . '">';

		include_once \Component::path('com_support') . DS . 'models' . DS . 'message.php';
		$messages = \Components\Support\Models\Message::all()->rows();

		$html[] = '<option value="0"' . (!$this->value ? ' selected="selected"' : '') . '>[ none ]</option>';

		foreach ($messages as $anode)
		{
			$html[] = '<option value="' . $anode->id . '"' . ($this->value == $anode->id ? ' selected="selected"' : '') . '>' . stripslashes($anode->title) . '</option>';
		}

		$html[] = '</select>';

		return implode("\n", $html);
	}
}
