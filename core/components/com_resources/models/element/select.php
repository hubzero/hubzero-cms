<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Element;

use Components\Resources\Models\Element as Base;
use Lang;

/**
 * Renders a list element
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
		$class = (isset($element->class)) ? 'class="' . $element->class . '"' : 'class="inputbox"';

		$options = array();
		if (!$element->required)
		{
			$options[] = \Html::select('option', '', Lang::txt('COM_RESOURCES_SELECT'));
		}
		foreach ($element->options as $option)
		{
			$val  = $option->value;
			$text = $option->label;
			$options[] = \Html::select('option', $val, $text);
		}

		return '<span class="field-wrap">' . \Html::select('genericlist',  $options, $control_name . '[' . $name . ']', $class, 'value', 'text', $value, $control_name . '-' . $name) . '</span>';
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
		$html[] = '<caption>' . Lang::txt('COM_RESOURCES_LIST_OPTION_HELP') . '</caption>';
		$html[] = '<tfoot>';
		$html[] = '<tr>';
		$html[] = '<td colspan="2" class="option-button"><button data-rel="'.$name.'" class="add-custom-option"><span>' . Lang::txt('COM_RESOURCES_NEW_OPTION') . '</span></button></td>';
		$html[] = '</tr>';
		$html[] = '</tfoot>';
		$html[] = '<tbody>';
		foreach ($element->options as $option)
		{
			$html[] = '<tr>';
			$html[] = '<td><label for="'. $control_name . '-' . $name . '-label-' . $k . '">' . Lang::txt('COM_RESOURCES_OPTION') . '</label></td>';
			$html[] = '<td><input type="text" size="35" name="' . $control_name . '[' . $name . '][options][' . $k . '][label]" id="'. $control_name . '-' . $name . '-label-' . $k . '" value="' . $option->label . '" /></td>';
			$html[] = '</tr>';

			$k++;
		}
		$html[] = '</tbody>';
		$html[] = '</table>';

		return implode("\n", $html);
	}
}
