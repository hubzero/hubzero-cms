<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;


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
	 * @var  bool
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
			Document::addScript(\Request::base(true) . '/core/components/com_publications/models/element/assets/js/geo.js');
			$this->_script = true;
		}

		$size  = (isset($element->size)  ? 'size="' . $element->size . '"' : '');
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
}
