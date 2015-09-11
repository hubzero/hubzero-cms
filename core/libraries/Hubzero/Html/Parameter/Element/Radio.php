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
