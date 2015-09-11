<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Models;

use Hubzero\Base\Object;
use stdClass;

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
	 * @access protected
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	* Get the element name
	*
	* @access public
	* @return string type of the parameter
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
		$name	= $element->name;
		$label	= isset($element->label) ? $element->label : $element->name;
		$descr	= isset($element->description) ? $element->description : '';

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
	 * @param   string  $value   Data
	 * @return  string  Formatted string.
	 */
	public function display($value)
	{
		return $value;
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
		/*
		 * 	Some value checking, apparently, the resource importer breaks this.
		 */
		if (!is_object($value))
		{
			return "<{$prefix}{$tag}>{$value}</{$prefix}{$tag}>";
		}
	}
}