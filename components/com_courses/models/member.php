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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.php');

/**
 * Courses model class for a course
 */
class CoursesModelMember extends JObject
{
	/**
	 * CoursesTableAsset
	 * 
	 * @var object
	 */
	public $_tbl = NULL;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	private $_permissions = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Description for '_list_keys'
	 *
	 * @var array
	 */
	static $_list_keys = array('role', 'role_permissions');

	/**
	 * Constructor
	 * 
	 * @param      integer $id  Resource ID or alias
	 * @param      object  &$db JDatabase
	 * @return     void
	 */
	public function __construct($uid, $oid=0)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableMember($this->_db);

		if (is_numeric($uid) || is_string($uid))
		{
			$this->_tbl->load($uid, $oid);
		}
		else if (is_object($uid))
		{
			$this->_tbl->bind($uid);
			if (isset($uid->role))
			{
				$this->_tbl->set('role', $uid->role);
			}
			if (isset($uid->role_permissions))
			{
				$this->_tbl->set('role_permissions', $uid->role_permissions);
			}
		}
		else if (is_array($uid))
		{
			$this->_tbl->bind($uid);
			if (isset($uid['role']))
			{
				$this->_tbl->set('role', $uid['role']);
			}
			if (isset($uid['role_permissions']))
			{
				$this->_tbl->set('role_permissions', $uid['role_permissions']);
			}
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$permissions = new $paramsClass($this->get('role_permissions'));
		$permissions->merge(new $paramsClass($this->get('permissions')));

		if ($this->exists())
		{
			$permissions->set('access-view-offering', true);
		}

		$this->set('permissions', $permissions);
	}

	/**
	 * Returns a reference to a wiki page object
	 *
	 * This method must be invoked as:
	 *     $inst = CoursesInstance::getInstance($alias);
	 *
	 * @param      string $pagename The page to load
	 * @param      string $scope    The page scope
	 * @return     object WikiPage
	 */
	static function &getInstance($uid=null, $oid=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid . '_' . $uid])) 
		{
			$instances[$oid . '_' . $uid] = new CoursesModelMember($uid, $oid);
		}

		return $instances[$oid . '_' . $uid];
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	/*public function __isset($property)
	{
		return isset($this->_data[$property]);
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	/*public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	/*public function __get($property)
	{
		if (isset($this->_tbl->$property)) 
		{
			return $this->_tbl->$property;
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
		if (in_array($property, self::$_list_keys))
		{
			if (!array_key_exists($property, get_object_vars($this->_tbl)))
			{
				if (is_object($this->_db))
				{
					require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'role.php');

					$result = new CoursesTableRole($this->_db);
					if ($result->load($this->_tbl->get('role_id')))
					{
						$this->_tbl->set('role', $result->title);
						$this->_tbl->set('role_permissions', $result->permissions);
					}
					/*$this->_db->setQuery("SELECT r.* FROM #__courses_roles AS r WHERE r.`id`=" . $this->_db->Quote($this->_tbl->get('role_id')));

					if (($result = $this->_db->loadObject()))
					{
						$this->_tbl->role = $result->role;
						$this->_tbl->role_permissions = $result->permissions;
					}*/
				}
			}
		}
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
		if ((int) $this->get('offering_id') && (int) $this->get('user_id')) 
		{
			return true;
		}
		return false;
	}

	/**
	 * Delete an asset
	 *   Deleted asset_associations until there is only one
	 *   association left, then it deletes the association,
	 *   the asset record, and asset file(s)
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		return true;
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
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='')
	{
		if (!$action)
		{
			return $this->get('permissions');
		}
		return $this->get('permissions')->get('access-' . strtolower($action) . '-offering');
	}
}

