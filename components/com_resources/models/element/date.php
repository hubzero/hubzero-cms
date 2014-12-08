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
 * Renders a category element
 */
class ResourcesElementDate extends ResourcesElement
{
	/**
	* Element name
	*
	* @var  string
	*/
	protected $_name = 'Date';

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
		$c = 0;
		if (isset($element->year) && $element->year)
		{
			$c++;
		}
		if (isset($element->month) && $element->month)
		{
			$c++;
		}
		if (isset($element->day) && $element->day)
		{
			$c++;
		}

		if ($c <= 1)
		{
			return parent::fetchTooltip($label, $description, $element, $control_name, $name);
		}
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
		$html = array();

		$c = 0;
		if (isset($element->year) && $element->year)
		{
			$c++;
		}
		if (isset($element->month) && $element->month)
		{
			$c++;
		}
		if (isset($element->day) && $element->day)
		{
			$c++;
		}

		if ($c > 1)
		{
			$html[] = '<fieldset>';

			$label = $element->label ? $element->label : $element->name;

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
		}

		if (isset($element->year) && $element->year)
		{
			$year = $this->_getValue('year', $value);

			// Get the year range
			// 0 = start
			// 1 = end
			if ($element->options)
			{
				$k = 0;
				foreach ($element->options as $key => $option)
				{
					if ($k == 0)
					{
						$i = $option->value;
					}
					if ($k == 1)
					{
						$y = $option->value;
					}
					$k++;
				}
			}

			// Set defaults if no date range available
			$i = (isset($i) && $i) ? $i : 1950;
			$y = (isset($y) && $y) ? $y : date("Y");

			// Build the list of years
			$options = array();
			$y++;
			for ($i, $n=$y; $i < $n; $i++)
			{
				$options[] = JHTML::_('select.option', $i, $i);
			}

			$options = array_reverse($options);
			array_unshift($options, JHTML::_('select.option', '0', JText::_('Year...')));

			$html[] = JHTML::_('select.genericlist', $options, $control_name . '[' . $name . '][year]', 'class="option"', 'value', 'text', $year, $control_name . '-' . $name . '-year');
		}

		if (isset($element->month) && $element->month)
		{
			$month = $this->_getValue('month', $value);

			// Build the list of years
			$options = array(
				JHTML::_('select.option', '0', JText::_('Month...'))
			);
			$i = 1;
			$y = 13;
			for ($i, $n=$y; $i < $n; $i++)
			{
				$options[] = JHTML::_('select.option', $i, $this->_getMonth($i));
			}

			$html[] = JHTML::_('select.genericlist', $options, $control_name . '[' . $name . '][month]', 'class="option"', 'value', 'text', $month, $control_name . '-' . $name . '-month');
		}

		if (isset($element->day) && $element->day)
		{
			$day = $this->_getValue('day', $value);

			// Build the list of years
			$options = array(
				JHTML::_('select.option', '0', JText::_('Day...'))
			);
			$i = 1;
			$y = 32;
			for ($i, $n=$y; $i < $n; $i++)
			{
				$options[] = JHTML::_('select.option', $i, $i);
			}

			$html[] = JHTML::_('select.genericlist', $options, $control_name . '[' . $name . '][day]', 'class="option"', 'value', 'text', $day, $control_name . '-' . $name . '-day');
		}

		if ($c > 1)
		{
			$html[] = '</fieldset>';
		}
		return '<span class="field-wrap">' . implode("\n", $html) . '</span>';
	}

	/**
	 * Return month text based on numerical value (1-12)
	 *
	 * @param   integer  $month Month numerical value
	 * @return  string
	 */
	private function _getMonth($month)
	{
		switch ($month)
		{
			case 1: $monthname = JText::_('January');   break;
			case 2: $monthname = JText::_('February');  break;
			case 3: $monthname = JText::_('March');     break;
			case 4: $monthname = JText::_('April');     break;
			case 5: $monthname = JText::_('May');       break;
			case 6: $monthname = JText::_('June');      break;
			case 7: $monthname = JText::_('July');      break;
			case 8: $monthname = JText::_('August');    break;
			case 9: $monthname = JText::_('September'); break;
			case 10: $monthname = JText::_('October');   break;
			case 11: $monthname = JText::_('November');  break;
			case 12: $monthname = JText::_('December');  break;
			default: $monthname = $month; break;
		}
		return $monthname;
	}

	/**
	 * Return a value from tag wrappers
	 *
	 * @param   string  $tag  Wrapper tags to match
	 * @param   string  $text Data
	 * @return  string
	 */
	private function _getValue($tag='lat', $text)
	{
		$pattern = "/<$tag>(.*?)<\/$tag>/i";
		preg_match($pattern, $text, $matches);
		return (isset($matches[1]) ? $matches[1] : '');
	}

	/**
	 * Display a value
	 *
	 * @param   string  $value   Data
	 * @return  string  Formatted string.
	 */
	public function display($value)
	{
		$year  = intval($this->_getValue('year', $value));
		$month = intval($this->_getValue('month', $value));
		$day   = intval($this->_getValue('day', $value));

		$html = '';
		if ($day && $day != 0)
		{
			$html .= $day . ' ';
		}
		if ($month && $month != 0)
		{
			$html .= $this->_getMonth($month) . ' ';
		}
		if ($year && $year != 0)
		{
			$html .= $year;
		}
		return $html;
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

		if (!isset($element->options))
		{
			$element->options = array();
		}

		if (count($element->options) < 1)
		{
			$opt = new stdClass;
			$opt->label = '1950';
			$opt->value = '1950';

			$element->options[] = $opt;
		}
		if (count($element->options) < 2)
		{
			$opt = new stdClass;
			$opt->label = '';
			$opt->value = '';

			$element->options[] = $opt;
		}

		$k = 0;

		$html[] = '<table class="admintable" id="'.$name.'">';
		$html[] = '<tbody>';
		$html[] = '<tr>';
		$html[] = '<td><label for="'. $control_name . '-' . $name . '-year">' . JText::_('Year') . '</label></td>';
		$html[] = '<td><input type="checkbox" name="' . $control_name . '[' . $name . '][year]" id="'. $control_name . '-' . $name . '-year" value="1" ' . (isset($element->year) && $element->year == 1 ? 'checked="checked"' : '') . ' /></td>';
		if (isset($element->options) && is_array($element->options))
		{
			foreach ($element->options as $option)
			{
				$html[] = '<td><label for="'. $control_name . '-' . $name . '-label-' . $k . '">' . ($k == 0 ? JText::_('Start') : JText::_('End')) . '</label></td>';
				$html[] = '<td><input type="text" size="4" name="' . $control_name . '[' . $name . '][options][' . $k . '][label]" id="'. $control_name . '-' . $name . '-label-' . $k . '" value="' . ($k == 0 ? ($option->label ? $option->label : 1950) : $option->label) . '" /></td>';

				$k++;
			}
		}
		$html[] = '</tr>';
		$html[] = '<tr>';
		$html[] = '<td><label for="'. $control_name . '-' . $name . '-month">' . JText::_('Month') . '</label></td>';
		$html[] = '<td colspan="3"><input type="checkbox" name="' . $control_name . '[' . $name . '][month]" id="'. $control_name . '-' . $name . '-month" value="1" ' . (isset($element->month) && $element->month == 1 ? 'checked="checked"' : '') . ' /></td>';
		$html[] = '</tr>';
		$html[] = '<tr>';
		$html[] = '<td><label for="'. $control_name . '-' . $name . '-day">' . JText::_('Day') . '</label></td>';
		$html[] = '<td colspan="3"><input type="checkbox" name="' . $control_name . '[' . $name . '][day]" id="'. $control_name . '-' . $name . '-day" value="1" ' . (isset($element->day) && $element->day == 1 ? 'checked="checked"' : '') . ' /></td>';
		$html[] = '</tr>';
		$html[] = '</tbody>';
		$html[] = '</table>';

		return implode("\n", $html);
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