<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Html\Parameter;

use Hubzero\Base\Object;

/**
 * Parameter base class
 *
 * The Element is the base class for all Element types
 */
class Element extends Object
{
	/**
	 * Element name
	 *
	 * This has to be set in the final
	 * renderer classes.
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * Reference to the object that instantiated the element
	 *
	 * @var  object
	 */
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @param   string  $parent  Element parent
	 * @return  void
	 */
	public function __construct($parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	 * Get the element name
	 *
	 * @return  string  type of the parameter
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Method to render an xml element
	 *
	 * @param   string  &$xmlElement   Name of the element
	 * @param   string  $value         Value of the element
	 * @param   string  $control_name  Name of the control
	 * @return  array   Attributes of an element
	 */
	public function render(&$xmlElement, $value, $control_name = 'params')
	{
		$name  = (string) $xmlElement['name'];
		$label = (string) $xmlElement['label'];
		$descr = (string) $xmlElement['description'];

		//make sure we have a valid label
		$label = $label ? $label : $name;
		$result[0] = $this->fetchTooltip($label, $descr, $xmlElement, $control_name, $name);
		$result[1] = $this->fetchElement($name, $value, $xmlElement, $control_name);
		$result[2] = $descr;
		$result[3] = $label;
		$result[4] = $value;
		$result[5] = $name;

		return $result;
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
		$output = '<label id="' . $control_name . $name . '-lbl" for="' . $control_name . $name . '"';
		if ($description)
		{
			$output .= ' class="hasTip" title="' . \App::get('language')->txt($label) . '::' . \App::get('language')->txt($description) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= \App::get('language')->txt($label) . '</label>';

		return $output;
	}

	/**
	 * Fetch an element
	 *
	 * @param   string  $name          Name attribute of the element
	 * @param   string  $value         Value attribute of the element
	 * @param   object  &$xmlElement   Element object
	 * @param   string  $control_name  Control name of the element
	 * @return  void
	 */
	public function fetchElement($name, $value, &$xmlElement, $control_name)
	{
	}
}
