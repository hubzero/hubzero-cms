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
 * Renders a checkbox element
 */
class ResourcesElementCheckbox extends ResourcesElement
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

		$output = '<legend id="' . $control_name.$name . '-lgd"';
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
				}
				else
				{
					$sel .= ($option->value == $value ? ' checked="checked"' : '');
				}

				$html[] = '<label for="'. $control_name . '-' . $name . $option->value . '">';
				$html[] = '<input class="option" type="checkbox" name="' . $control_name . '[' . $name . '][]" id="'. $control_name . '-' . $name . $option->value . '" value="' . $option->value . '"' . $sel . ' />';
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

		$html[] = '<table class="admintable" id="'.$name.'">';
		$html[] = '<tfoot>';
		$html[] = '<tr>';
		$html[] = '<td colspan="4" class="option-button"><button rel="'.$name.'" class="add-custom-option"><span>' . JText::_('COM_RESOURCES_NEW_OPTION') . '</span></button></td>';
		$html[] = '</tr>';
		$html[] = '</tfoot>';
		$html[] = '<tbody>';
		if (isset($element->options) && is_array($element->options))
		{
			foreach ($element->options as $option)
			{
				$html[] = '<tr>';
				$html[] = '<td><label for="'. $control_name . '-' . $name . '-label-' . $k . '">' . JText::_('COM_RESOURCES_OPTION') . '</label></td>';
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

	/**
	 * Create html tag for element.
	 * 
	 * @param  string $tag    Tag Name
	 * @param  sting  $value  Tag Value
	 * @param  string $prefix Tag prefix
	 * @return string HTML
	 */
	public function toHtmlTag($tag, $value, $prefix = 'nb:')
	{
		// array to hold date parts
		$parts = array();

		// case value to array (in case object)
		$value = array_filter((array) $value);

		// loop through each value prop
		foreach ($value as $k => $v)
		{
			array_push($parts, "<{$k}>{$v}</{$k}>");
		}

		// build and return tag
		$html  = "<{$prefix}{$tag}>";
		$html .= implode("\n", $parts);
		$html .= "</{$prefix}{$tag}>";
		return $html;
	}
}