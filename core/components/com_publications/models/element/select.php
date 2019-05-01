<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;
use Html;
use Lang;

/**
 * Renders a select list element
 */
class Select extends Base
{
	/**
  * Element type
  *
  * @var  string
  */
	protected $_name = 'Select list';

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
		$class = (isset($element->class)) ? 'class="'.$element->class.'"' : 'class="inputbox"';

		$options = array();
		if (!$element->required)
		{
			$options[] = Html::select('option', '', Lang::txt('Select...'));
		}
		foreach ($element->options as $option)
		{
			$val	= $option->value;
			$text	= $option->label;
			$options[] = Html::select('option', $val, Lang::txt($text));
		}

		return '<span class="field-wrap">' . Html::select('genericlist', $options, $control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.'-'.$name) . '</span>';
	}

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value to check against
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  string  HTML
	 */
	public function fetchOptions($name, $value, &$element, $control_name)
	{
		$html = array();

		$k = 0;

		$html[] = '<table class="admintable" id="'.$name.'">';
		$html[] = '<caption>' . Lang::txt('Lists include blank "Select..." option unless made a required field') . '</caption>';
		$html[] = '<tfoot>';
		$html[] = '<tr>';
		$html[] = '<td colspan="4" class="option-button"><button rel="'.$name.'" class="add-custom-option"><span>' . Lang::txt('COM_PUBLICATIONS_NEW_OPTION') . '</span></button></td>';
		$html[] = '</tr>';
		$html[] = '</tfoot>';
		$html[] = '<tbody>';
		foreach ($element->options as $option)
		{
			$html[] = '<tr>';
			$html[] = '<td><label for="'. $control_name . '-' . $name . '-label-' . $k . '">' . Lang::txt('Option') . '</label></td>';
			$html[] = '<td><input type="text" size="35" name="' . $control_name . '[' . $name . '][options][' . $k . '][label]" id="'. $control_name . '-' . $name . '-label-' . $k . '" value="' . $option->label . '" /></td>';
			$html[] = '</tr>';

			$k++;
		}
		$html[] = '</tbody>';
		$html[] = '</table>';

		return implode("\n", $html);
	}
}
