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

namespace Hubzero\Html\Toolbar;

use Hubzero\Base\Object;

/**
 * Button base class
 * The Button is the base class for all Button types
 *
 * Inspired by Joomla's JButton class
 */
abstract class Button extends Object
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = null;

	/**
	 * Reference to the object that instantiated the element
	 *
	 * @var  object  Button
	 */
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @param   object  $parent  The parent
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
	 * Get the HTML to render the button
	 *
	 * @param   array  &$definition  Parameters to be passed
	 * @return  string
	 */
	public function render(&$definition)
	{
		// Initialise some variables
		$html   = null;
		$cls    = array();
		if (isset($definition[9]))
		{
			$cls = array_pop($definition);
		}
		$id     = call_user_func_array(array(&$this, 'fetchId'), $definition);
		$action = call_user_func_array(array(&$this, 'fetchButton'), $definition);

		// Build id attribute
		if ($id)
		{
			$id = 'id="' . $id . '"';
		}

		// Build the HTML Button
		$html .= '<li class="button ' . implode(' ', $cls) . '" ' . $id . ">\n";
		$html .= $action;
		$html .= "</li>\n";

		return $html;
	}

	/**
	 * Method to get the CSS class name for an icon identifier
	 *
	 * @param   string  $identifier  Icon identification string
	 * @return  string  CSS class name
	 */
	public function fetchIconClass($identifier)
	{
		return "icon-32-$identifier";
	}

	/**
	 * Get the button
	 *
	 * @return  string
	 */
	abstract public function fetchButton();
}
