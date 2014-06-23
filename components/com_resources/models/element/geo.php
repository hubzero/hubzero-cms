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
 * Renders a geolocation element
 */
class ResourcesElementGeo extends ResourcesElement
{
	/**
	* Element name
	*
	* @var  string
	*/
	protected $_name = 'Geo Location';

	/**
	* Flag for if JS has been pushed to document or not
	*
	* @var  string
	*/
	protected $_script = false;

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
		$output = '<label id="' . $control_name . '-' . $name . '-lbl" for="' . $control_name . '-' . $name . '"';
		if ($description)
		{
			$output .= ' class="hasTip" title="' . JText::_($label) . '::' . JText::_($description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= JText::_($label) . ' <span class="hint">' . JText::_('(street, city, state/province postal-code, country)') . '</span>';
		$output .= (isset($element->required) && $element->required) ? ' <span class="required">' . JText::_('JOPTION_REQUIRED') . '</span>' : '';
		$output .= '</label>';

		return $output;
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
		if (!$this->_script)
		{
			$document = JFactory::getDocument();
			$document->addScript('//maps.google.com/maps/api/js?sensor=false');
			$document->addScript(JURI::base(true) . '/components/com_resources/models/element/geo.js');
			$this->_script = true;
		}

		$size  = (isset($element->size)  ? 'size="' . $element->size . '"'               : '');
		$class = (isset($element->class) ? 'class="geolocation ' . $element->class . '"' : 'class="geolocation"');

		$address = $this->_getValue('value', $value);
		$lat = $this->_getValue('lat', $value);
		$lat = (trim($lat)) ? $lat : '0.0';
		$lng = $this->_getValue('lng', $value);
		$lng = (trim($lng)) ? $lng : '0.0';

		$value = preg_replace('/<lat>(.*?)<\/lat>/i', '', $value);
		$value = preg_replace('/<lng>(.*?)<\/lng>/i', '', $value);

		/*
		 * Required to avoid a cycle of encoding &
		 * html_entity_decode was used in place of htmlspecialchars_decode because
		 * htmlspecialchars_decode is not compatible with PHP 4
		 */
		$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

		$html  = '<input type="text" name="' . $control_name . '[' . $name . '][value]" id="' . $control_name . '-' . $name . '" value="' . $address . '" ' . $class . ' ' . $size . ' />';
		$html .= '<input type="hidden" name="' . $control_name . '[' . $name . '][lat]" id="' . $control_name . '-' . $name . '-lat" value="' . $lat . '" />';
		$html .= '<input type="hidden" name="' . $control_name . '[' . $name . '][lng]" id="' . $control_name . '-' . $name . '-lng" value="' . $lng . '" />';

		return $html;
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
		return trim($this->_getValue('value', $value));
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