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

define('COURSES_STATE_UNPUBLISHED', 0);
define('COURSES_STATE_PUBLISHED',   1);
define('COURSES_STATE_DELETED',     2);

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'log.php');

/**
 * Courses model class for a course
 */
abstract class CoursesModelAbstract extends JObject
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
	 * Date keys coming from 
	 * #__courses_offering_section_dates
	 *
	 * @var array
	 */
	static $_section_keys = array(
		//'section_id',
		'publish_up', 
		'publish_down'
	);

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
					if (!array_key_exists($key, $properties)) // && in_array($property, self::$_section_keys))
					{
						$this->_tbl->set($key, $property);
					}
				}
			}
			else if (is_array($oid))
			{
				$this->_tbl->bind($oid);

				$properties = $this->_tbl->getProperties();
				foreach (array_keys($oid) as $key)
				{
					if (!array_key_exists($key, $properties)) // && in_array($property, self::$_section_keys))
					{
						$this->_tbl->set($key, $oid[$key]);
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
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isPublished()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return true;
		}
		if ($this->get('state') == COURSES_STATE_PUBLISHED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isUnpublished()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return false;
		}
		if ($this->get('state') == COURSES_STATE_UNPUBLISHED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function isDeleted()
	{
		if (!in_array('state', array_keys($this->_tbl->getProperties())))
		{
			return false;
		}
		if ($this->get('state') == COURSES_STATE_DELETED) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 * 
	 * @return     boolean
	 */
	public function started()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished()) 
		{
			return false;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->get('publish_up') 
		 && $this->get('publish_up') != '0000-00-00 00:00:00' 
		 && $this->get('publish_up') > $now) 
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the offering ended?
	 * 
	 * @return     boolean
	 */
	public function ended()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished()) 
		{
			return true;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->get('publish_down') 
		 && $this->get('publish_down') != '0000-00-00 00:00:00' 
		 && $this->get('publish_down') <= $now) 
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the offering is available
	 * 
	 * @return     boolean
	 */
	public function isAvailable()
	{
		// If it doesn't exist or isn't published
		if (!$this->exists() || !$this->isPublished())
		{
			return false;
		}

		// Make sure the item is published and within the available time range
		if ($this->started() && !$this->ended()) 
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
		// Get some data for the log
		$log = new stdClass;
		foreach ($this->_tbl->getProperties() as $key => $value)
		{
			$log->$key = $value;
		}
		$log = json_encode($log);

		// Get the scope ID
		$scope_id = $this->get('id');

		// Remove record from the database
		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		// Log the event
		$this->log($scope_id, $this->_scope, 'delete', $log);

		// Hey, no errors!
		return true;
	}

	/**
	 * Log an action
	 *
	 * @param     integer $scope_id Scope ID
	 * @param     string  $scope    Scope
	 * @param     string  $action   Action performed
	 * @param     string  $log      Data
	 * @return    void
	 */
	public function log($scope_id, $scope, $action, $log=null)
	{
		$juser = JFactory::getUser();

		$log = new CoursesTableLog($this->_db);
		$log->scope_id  = $scope_id;
		$log->scope     = $scope;
		$log->user_id   = $juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action    = $action;
		$log->comments  = $log;
		$log->actor_id  = $juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}
	}
}

