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

if (!defined('ANSWERS_DATE_FORMAT'))
{
	if (version_compare(JVERSION, '1.6', 'ge'))
	{
		define('ANSWERS_DATE_YEAR', "Y");
		define('ANSWERS_DATE_MONTH', "m");
		define('ANSWERS_DATE_DAY', "d");
		define('ANSWERS_DATE_TIMEZONE', false);
		define('ANSWERS_DATE_FORMAT', 'd M Y');
		define('ANSWERS_TIME_FORMAT', 'h:i A');
	}
	else
	{
		define('ANSWERS_DATE_YEAR', "%Y");
		define('ANSWERS_DATE_MONTH', "%m");
		define('ANSWERS_DATE_DAY', "%d");
		define('ANSWERS_DATE_TIMEZONE', 0);
		define('ANSWERS_DATE_FORMAT', '%d %b %Y');
		define('ANSWERS_TIME_FORMAT', '%I:%M %p');
	}
}

/**
 * Courses model class for a course
 */
abstract class AnswersModelAbstract extends JObject
{
	/**
	 * CoursesTableAsset
	 * 
	 * @var object
	 */
	protected $_tbl_name = null;

	/**
	 * CoursesTableAsset
	 * 
	 * @var object
	 */
	protected $_scope = NULL;

	/**
	 * CoursesTableAsset
	 * 
	 * @var object
	 */
	protected $_tbl = NULL;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	protected $_creator = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	protected $_db = NULL;

	/**
	 * JParameter
	 * 
	 * @var object
	 */
	protected $_config = NULL;

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (is_numeric($oid) || is_string($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_object($oid))
			{
				$this->_tbl->bind($oid);

				$properties = $this->_tbl->getProperties();
				foreach (get_object_vars($oid) as $key => $property)
				{
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $property);
					}
				}
			}
			else if (is_array($oid))
			{
				$this->_tbl->bind($oid);

				$properties = $this->_tbl->getProperties();
				foreach (array_keys($oid) as $key)
				{
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $oid[$key]);
					}
				}
			}
		}
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
 	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property})) 
		{
			return $this->_tbl->{'__' . $property};
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed Previous value of the property
	 */
	public function set($property, $value = null)
	{
		if (!array_key_exists($property, $this->_tbl->getProperties()))
		{
			$property = '__' . $property;
		}
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the resource exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function exists()
	{
		if ($this->get('id') && (int) $this->get('id') > 0) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Get the creator of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		if (!isset($this->_creator) || !is_object($this->_creator))
		{
			$this->_creator = JUser::getInstance($this->get('created_by'));
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isReported()
	{
		if ($this->get('reports', -1) > 0)
		{
			return true;
		}
		// Reports hasn't been set
		if ($this->get('reports', -1) == -1) 
		{
			if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php')) 
			{
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');
				$ra = new ReportAbuse($this->_db);
				$val = $ra->getCount(array(
					'id'       => $this->get('id'), 
					'category' => 'answer'
				));
				$this->set('reports', $val);
				if ($this->get('reports') > 0)
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check if the course exists
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function bind($data=null)
	{
		return $this->_tbl->bind($data);
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		// Ensure we have a database to work with
		if (empty($this->_db))
		{
			return false;
		}

		// Validate data?
		if ($check)
		{
			// Is data valid?
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		// Attempt to store data
		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete a record
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove record from the database
		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		// Hey, no errors!
		return true;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @return     boolean True if authorized, false if not
	 */
	public function config($key=null)
	{
		if (!isset($this->_config))
		{
			$this->_config =& JComponentHelper::getParams('com_answers');
		}
		if ($key)
		{
			if ($key == 'banking')
			{
				$this->_config->set('banking', JComponentHelper::getParams('com_members')->get('bankAccounts'));
			}
			return $this->_config->get($key);
		}
		return $this->_config;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view', $item='entry')
	{
		return $this->config()->get('access-' . strtolower($action) . '-' . $item);
	}
}

