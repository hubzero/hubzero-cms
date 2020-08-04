<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;
use App;

/**
 * Renders a radio element
 */
class Radio extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Radio';

	/**
	 * Fetch a calendar element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$options = array();
		foreach ($node->children() as $option)
		{
			$val  = (string) $option['value'];
			$text = (string) $option;
			$options[] = Builder\Select::option($val, $text);
		}

		return Builder\Select::radiolist($options, '' . $control_name . '[' . $name . ']', 'class="option"', 'value', 'text', $value, $control_name . $name, true) . '</fieldset>';
	}

	/**
	 * Method to get a tool tip from an XML element
	 *
	 * @param   string  $label         Label attribute for the element
	 * @param   string  $description   Description attribute for the element
	 * @param   object  &$xmlElement   The element object
	 * @param   string  $control_name  Control name
	 * @param   string  $name          Name attribut
	 * @return  string
	 */
	public function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
	{
		$output = '<fieldset id="' . $control_name . $name . '-lbl" class="radio" data-for="' . $control_name . $name . '"><legend';
		if ($description)
		{
			$output .= ' class="hasTip" title="' . App::get('language')->txt($label) . '::' . App::get('language')->txt($description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= App::get('language')->txt($label) . '</legend>';

		return $output;
	}
}
