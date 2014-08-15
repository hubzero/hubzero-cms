<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * Renders a radio element
 */
class ResourcesElementRadio extends ResourcesElement
{
	/**
	* Element name
	*
	* @var  string
	*/
	protected $_name = 'Radio boxes';

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

		$html = array();
		$html[] = '<fieldset>';

		$output = '<legend id="' . $control_name . $name . '-lgd"';
		if (isset($element->description) && $element->description)
		{
			$output .= ' class="hasTip" title="' . JText::_($label) . '::' . JText::_($element->description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= JText::_($label);
		$output .= (isset($element->required) && $element->required) ? ' <span class="required">' . JText::_('JOPTION_REQUIRED') . '</span>' : '';
		$output .= '</legend>';

		$html[] = $output;

		$k = 0;
		if (isset($element->options) && is_array($element->options))
		{
			foreach ($element->options as $option)
			{
				$sel = '';
				if (is_array($value))
				{
					foreach ($value as $val)
					{
						$k2 = is_object($val) ? $val->$key : $val;
						if ($value == $k2)
						{
							$sel .= ' selected="selected"';
							break;
						}
					}
				}
				else
				{
					$sel .= ($option->value == $value ? ' checked="checked"' : '');
				}

				$html[] = '<label for="'. $control_name . '-' . $name . $option->value . '">';
				$html[] = '<input class="option" type="radio" name="' . $control_name . '[' . $name . ']" id="'. $control_name . '-' . $name . $option->value . '" value="' . $option->value . '"' . $sel . ' />';
				$html[] = JText::_($option->label) . '</label>';

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

		$html[] = '<table class="admintable" id="' . $name . '">';
		$html[] = '<tfoot>';
		$html[] = '<tr>';
		$html[] = '<td colspan="2" class="option-button"><button data-rel="' . $name . '" class="add-custom-option"><span>' . JText::_('COM_RESOURCES_NEW_OPTION') . '</span></button></td>';
		$html[] = '</tr>';
		$html[] = '</tfoot>';
		$html[] = '<tbody>';
		if (isset($element->options) && is_array($element->options))
		{
			foreach ($element->options as $option)
			{
				$html[] = '<tr>';
				$html[] = '<td><label for="'. $control_name . '-' . $name . '-label-' . $k . '">' . JText::_('COM_RESOURCES_OPTION') . '</label></td>';
				$html[] = '<td><input type="text" size="35" name="' . $control_name . '[' . $name . '][options][' . $k . '][label]" id="' . $control_name . '-' . $name . '-label-' . $k . '" value="' . $option->label . '" /></td>';
				$html[] = '</tr>';

				$k++;
			}
		}
		$html[] = '</tbody>';
		$html[] = '</table>';

		return implode("\n", $html);
	}
}