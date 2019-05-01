<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;

/**
 * Renders a checkbox element
 */
class Checkbox extends Base
{
	/**
  * Element name
  *
  * @var  string
  */
	protected $_name = 'Checkboxes';

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $label         Display name of the field
	 * @param   string  $description   Description for the field
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @param   string  $name          Name of the field
	 * @return  string  HTML
	 */
	public function fetchTooltip($label, $description, &$element, $control_name='', $name='')
	{
		return '';
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
	public function fetchElement($name, $value, &$element, $control_name)
	{
		$label = $element->label ? $element->label : $element->name;

		$values = array();

		$pattern = "/<\d>(.*?)<\/\d>/i";
		preg_match_all($pattern, $value, $matches);
		if ($matches)
		{
			foreach ($matches[1] as $match)
			{
				$pattern = "/<\d>$match<\/\d>/i";
				$value = preg_replace($pattern, '', $value);
				$values[] = $match;
			}
		}
		$values[] = $value;

		$html = array();
		$html[] = '<fieldset>';

		$output = '<legend id="'.$control_name.$name.'-lgd"';
		if (isset($element->description) && $element->description)
		{
			$output .= ' class="hasTip" title="'.Lang::txt($label).'::'.Lang::txt($element->description).'">';
		}
		else
		{
			$output .= '>';
		}
		$output .= Lang::txt($label);
		$output .= (isset($element->required) && $element->required) ? ' <span class="required">'.Lang::txt('JOPTION_REQUIRED').'</span>' : '';
		$output .= '</legend>';

		$html[] = $output;
		$k = 0;
		if (isset($element->options) && is_array($element->options))
		{
			foreach ($element->options as $option)
			{
				$sel = '';
				if (is_array($values))
				{
					foreach ($values as $val)
					{
						$k2 = is_object($val) ? $val->$key : $val;
						if ($option->value == $k2)
						{
							$sel .= ' checked="checked"';
							break;
						}
					}
				} else {
					$sel .= ($option->value == $value ? ' checked="checked"' : '');
				}

				$html[] = '<label for="'. $control_name . '-' . $name . $option->value . '">';
				$html[] = '<input class="option" type="checkbox" name="' . $control_name . '[' . $name . '][]" id="'. $control_name . '-' . $name . $option->value . '" value="' . $option->value . '"' . $sel . ' />';
				$html[] = Lang::txt($option->label) . '</label>';

				$k++;
			}
		}
		$html[] = '</fieldset>';

		return implode("\n", $html);
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
		$html[] = '<tfoot>';
		$html[] = '<tr>';
		$html[] = '<td colspan="4" class="option-button"><button rel="'.$name.'" class="add-custom-option"><span>' . Lang::txt('COM_PUBLICATIONS_NEW_OPTION') . '</span></button></td>';
		$html[] = '</tr>';
		$html[] = '</tfoot>';
		$html[] = '<tbody>';
		if (isset($element->options) && is_array($element->options))
		{
			foreach ($element->options as $option)
			{
				$html[] = '<tr>';
				$html[] = '<td><label for="'. $control_name . '-' . $name . '-label-' . $k . '">' . Lang::txt('Option') . '</label></td>';
				$html[] = '<td><input type="text" size="35" name="' . $control_name . '[' . $name . '][options][' . $k . '][label]" id="'. $control_name . '-' . $name . '-label-' . $k . '" value="' . $option->label . '" /></td>';
				$html[] = '</tr>';

				$k++;
			}
		}
		$html[] = '</tbody>';
		$html[] = '</table>';

		return implode("\n", $html);
	}

	/**
	 * Display the language for a language code
	 *
	 * @param   string  $value   Data
	 * @return  string  Formatted string.
	 */
	public function display($value)
	{
		$values = array(
			'<ul>'
		);

		$pattern = "/<\d>(.*?)<\/\d>/i";
		preg_match_all($pattern, $value, $matches);
		if ($matches)
		{
			foreach ($matches[1] as $match)
			{
				$pattern = "/<\d>$match<\/\d>/i";
				$value = preg_replace($pattern, '', $value);
				$values[] = '<li>' . $match . '</li>';
			}
		}

		$values[] = '</ul>';

		return implode("\n", $values);
	}
}
