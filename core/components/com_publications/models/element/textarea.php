<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;

/**
 * Renders a textarea element
 */
class Textarea extends Base
{
	/**
  * Element name
  *
  * @var		string
  */
	protected	$_name = 'Textarea';

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value to check against
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  string  HTML
	 */
	public function fetchElement($name, $value, &$element, $control_name)
	{
		$rows = isset($element->rows) ? $element->rows : 6;
		$cols = isset($element->cols) ? $element->cols : 50;
		$class = isset($element->class) ? 'class="'.$element->class.'"' : 'class="text_area"';
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return '<span class="field-wrap"><textarea id="' . $control_name.'-'.$name . '" name="' . $control_name.'['.$name.']' . '" rows="' . $rows . '" cols="' . $cols . '">' . $value . '</textarea></span>';
	}
}
