<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Renders a list of support ticket statuses
 */
class Ticketstate extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Ticketstate';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$value = $this->value;
		$name  = $this->name;
		$id    = $this->id;

		$html = array();

		$html[] = '<select name="' . $name . '" id="' . $id . '">';

		include_once \Component::path('com_support') . DS . 'models' . DS . 'status.php';

		$status = \Components\Support\Models\Status::all()
			->order('open', 'desc')
			->rows();

		$html[] = '<option value=""' . ($value === '' || $value === null ? ' selected="selected"' : '') . '>--</option>';
		$html[] = '<option value="0"' . ($value === 0 || $value === '0' ? ' selected="selected"' : '') . '>open: New</option>';

		$switched = false;
		foreach ($status as $anode)
		{
			if (!$anode->open && !$switched)
			{
				$html[] = '<option value="-1"' . ($value == -1 ? ' selected="selected"' : '') . '>closed: No resolution</option>';
				$switched = true;
			}
			$html[] = '<option value="' . $anode->id . '"' . ($value == $anode->id ? ' selected="selected"' : '') . '>' . ($anode->open ? 'open: ' : 'closed: ') . stripslashes($anode->title) . '</option>';
		}

		$html[] = '</select>';

		return implode("\n", $html);
	}
}
