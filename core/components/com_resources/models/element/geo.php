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
use Document;
use Lang;

/**
 * Renders a geolocation element
 */
class Geo extends Base
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
			$output .= ' class="hasTip" title="' . Lang::txt($label) . '::' . Lang::txt($description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= Lang::txt($label) . ' <span class="hint">' . Lang::txt('(street, city, state/province postal-code, country)') . '</span>';
		$output .= (isset($element->required) && $element->required) ? ' <span class="required">' . Lang::txt('JOPTION_REQUIRED') . '</span>' : '';
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
			Document::addScript('//maps.google.com/maps/api/js?sensor=false');
			Document::addScript(\Request::base(true) . '/core/components/com_resources/models/element/geo.js');
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

		// Required to avoid a cycle of encoding &
		// html_entity_decode was used in place of htmlspecialchars_decode because
		// htmlspecialchars_decode is not compatible with PHP 4
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
	 * @param   string  $tag     Tag Name
	 * @param   sting   $value   Tag Value
	 * @param   string  $prefix  Tag prefix
	 * @return  string  HTML
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
