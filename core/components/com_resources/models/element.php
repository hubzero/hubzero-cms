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

namespace Components\Resources\Models;

use Hubzero\Base\Object;
use stdClass;
use Lang;

/**
 * Resource element base class
 */
class Element extends Object
{
	/**
	* Element name
	*
	* This has to be set in the final
	* renderer classes.
	*
	* @var string
	*/
	protected $_name = null;

	/**
	* Reference to the object that instantiated the element
	*
	* @var object
	*/
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @param   object  $parent
	 * @return  void
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	* Get the element name
	*
	* @return  string  Type of the parameter
	*/
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Return any options this element may have
	 *
	 * @param   object  $element       Data Source Object.
	 * @param   string  $value         Selected value for the element
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  object  An object populated with all the data and HTML for an element
	 */
	public function render(&$element, $value, $control_name = 'fields')
	{
		$name  = $element->name;
		$label = isset($element->label) ? $element->label : $element->name;
		$descr = isset($element->description) ? $element->description : '';

		// Make sure we have a valid label
		$label = $label ? $label : $name;

		$result = new stdClass;
		$result->label = $this->fetchTooltip($label, $descr, $element, $control_name, $name);
		$result->element = $this->fetchElement($name, $value, $element, $control_name);
		$result->description = $descr;
		$result->text  = $label;
		$result->value = $value;
		$result->name  = $name;
		$result->type  = $element->type;

		return $result;
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
		$output = '<label id="' . $control_name . '-' . $name . '-lbl" for="' . $control_name . '-' . $name . '"';
		if ($description)
		{
			$output .= ' class="hasTip" title="' . $label . '::' . $description . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= $label;
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
		return Lang::txt('COM_RESOURCES_NONE');
	}

	/**
	 * Display a value
	 *
	 * @param   string  $value  Data
	 * @return  string  Formatted string.
	 */
	public function display($value)
	{
		return $value;
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
		// Some value checking, apparently, the resource importer breaks this.
		if (!is_object($value))
		{
			return "<{$prefix}{$tag}>{$value}</{$prefix}{$tag}>";
		}

		return '';
	}
}