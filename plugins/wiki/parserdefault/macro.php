<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Base class for wiki macros
 * Should be extended
 */
class WikiMacro
{
	/**
	 * Name of the macro
	 *
	 * @var string
	 */
	protected $_name  = NULL;

	/**
	 * Container for internal data
	 *
	 * @var array
	 */
	protected $_data  = array();

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	protected $_db    = NULL;

	/**
	 * Container for errors
	 *
	 * @var unknown
	 */
	protected $_error = NULL;

	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = false;

	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $linkLog = array();

	/**
	 * Instance of a macro
	 *
	 * @var object
	 */
	static protected $thisInstance = NULL;

	/**
	 * Constructor
	 *
	 * @param      array $config Configuration options
	 * @return     void
	 */
	public function __construct($config=array())
	{
		$this->_db = JFactory::getDBO();

		$this->args = '';

		// Set the controller name
		if (empty($this->_name))
		{
			if (isset($config['name']))
			{
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if (!preg_match('/(.*)Macro/i', get_class($this), $r))
				{
					echo 'Controller::__construct() : Can\'t get or parse class name.';
				}
				$this->_name = strtolower($r[1]);
			}
		}
	}

	/**
	 * Set a property
	 *
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 *
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Get an instance of this macro, creating it if not found
	 *
	 * @param      array $config Configuration parameters
	 * @return     object
	 */
	static public function getInstance($config=array())
	{
		if (self::$thisInstance == null)
		{
			if (isset($config['name']))
			{
				$name = $config['name'];
			}
			else
			{
				$name = get_class();
			}
			self::$thisInstance = new $name();
		}
		return self::$thisInstance;
	}

	/**
	 * Render macro output
	 * this should be overriden by extended classes
	 *
	 * @return     void
	 */
	public function render()
	{
		// Abstract function for overloading
	}

	/**
	 * Returns description of macro, use, and accepted arguments
	 * this should be overriden by extended classes
	 *
	 * @return     string
	 */
	public function description()
	{
		return JText::_('Not implemented.');
	}
}

