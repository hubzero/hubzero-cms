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
 * Renders a hidden element
 */
class ResourcesElementHidden extends ResourcesElement
{
	/**
	* Element name
	*
	* @var  string
	*/
	protected $_name = 'Hidden';

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
		$class = (isset($element->class)) ? $element->class : 'text_area';

		$val = $value;
		$k = 0;
		if (isset($element->options))
		{
			if (is_array($element->options))
			{
				foreach ($element->options as $option)
				{
					if ($k >= 1)
					{
						break;
					}

					$val = $option->value;

					$k++;
				}
			}
			else if (is_object($element->options))
			{
				$val = $element->options->value;
			}
		}

		return '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . '-' . $name . '" value="' . $val . '" class="' . $class . '" />';
	}

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
	public function fetchOptions($name, $value, &$element, $control_name)
	{
		$k = 0;

		$html[] = '<table class="admintable" id="' . $name . '">';
		$html[] = '<tbody>';
		if (isset($element->options) && is_array($element->options))
		{
			foreach ($element->options as $option)
			{
				if ($k >= 1)
				{
					break;
				}

				$html[] = '<tr>';
				$html[] = '<td><label for="'. $control_name . '-' . $name . '-label-' . $k . '">' . JText::_('COM_RESOURCES_VALUE') . '</label></td>';
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
		return '';
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
		// build and return tag
		return "<{$prefix}{$tag}>{$value}</{$prefix}{$tag}>";
	}

}