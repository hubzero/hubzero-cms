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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'offering.php');

/**
 * Courses model class for a course
 */
class CoursesModelOffering extends JObject
{
	/**
	 * Flag for if authorization checks have been run
	 * 
	 * @var mixed
	 */
	private $_authorized = false;

	/**
	 * CoursesTableInstance
	 * 
	 * @var object
	 */
	public $_tbl = NULL;

	/**
	 * CoursesModelIterator
	 * 
	 * @var object
	 */
	public $units = NULL;

	/**
	 * CoursesModelUnit
	 * 
	 * @var object
	 */
	public $unit = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_roles = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_members = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_member = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	//private $_instructor = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_creator = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * JParameter
	 * 
	 * @var object
	 */
	public $params = NULL;

	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $_data = array();

	/**
	 * Description for '_list_keys'
	 *
	 * @var array
	 */
	static $_list_keys = array('members');

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableOffering($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
		}

		$this->params = JComponentHelper::getParams('com_courses');
	}

	/**
	 * Returns a reference to a course offering model
	 *
	 * This method must be invoked as:
	 *     $offering = CoursesModelOffering::getInstance($alias);
	 *
	 * @param      mixed $oid ID (int) or alias (string)
	 * @return     object CoursesModelOffering
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid;
		}
		else if (is_object($oid))
		{
			$key = $oid->get('id');
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new CoursesModelOffering($oid);
		}

		return $instances[$key];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $default The default value
	 * @return	mixed The value of the property
	 * @see		getProperties()
	 * @since	1.5
 	 */
	public function get($property, $default=null)
	{
		if (in_array($property, self::$_list_keys))
		{
			if (!array_key_exists($property, get_object_vars($this->_tbl)))
			{
				if (is_object($this->_db))
				{
					$this->_db->setQuery("SELECT user_id from #__courses_offering_members WHERE offering_id=" . $this->_db->Quote($this->_tbl->get('id')));

					if (($results = $this->_db->loadResultArray()))
					{
						$this->_tbl->$property = $results;
					}
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
	 * @access	public
	 * @param	string $property The name of the property
	 * @param	mixed  $value The value of the property to set
	 * @return	mixed Previous value of the property
	 * @see		setProperties()
	 * @since	1.5
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
		if ($this->get('id') &&  (int) $this->get('id') > 0) 
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
	public function started()
	{
		if (!$this->exists()) 
		{
			return false;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->get('start_date') 
		 && $this->get('start_date') != '0000-00-00 00:00:00' 
		 && $this->get('start_date') > $now) 
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
		if (!$this->exists()) 
		{
			return true;
		}

		$now = date('Y-m-d H:i:s', time());

		if ($this->get('end_date') 
		 && $this->get('end_date') != '0000-00-00 00:00:00' 
		 && $this->get('end_date') <= $now) 
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
	public function available()
	{
		if (!$this->exists())
		{
			return false;
		}

		// Make sure the resource is published and standalone
		if ($this->started() && !$this->ended()) 
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to get/set the current unit
	 *
	 * @param     mixed $id ID or alias of specific unit
	 * @return    object CoursesModelUnit
	 */
	public function unit($id=null)
	{
		if (!isset($this->unit) 
		 || ($id !== null && (int) $this->unit->get('id') != $id && (string) $this->unit->get('alias') != $id))
		{
			$this->unit = null;

			if (isset($this->units))
			{
				foreach ($this->units() as $key => $unit)
				{
					if ((int) $unit->get('id') == $id || (string) $unit->get('alias') == $id)
					{
						$this->unit = $unit;
						$this->unit->siblings($this->units());
						break;
					}
				}
			}
			else
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');

				$this->unit = CoursesModelUnit::getInstance($id);
			}
		}
		return $this->unit;
	}

	/**
	 * Get a list of units for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function units($filters=array())
	{
		if (!isset($this->units) || !is_a($this->units, 'CoursesModelIterator'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'unit.php');

			$tbl = new CoursesTableUnit($this->_db);

			$filters['offering_id'] = (int) $this->get('id');

			if (($results = $tbl->find($filters)))
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');

				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelUnit($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->units = new CoursesModelIterator($results);
		}

		return $this->units;
	}

	/**
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 * 
	 * @return     boolean
	 */
	public function manager()
	{
		return $this->access('manage'); //in_array(JFactory::getUser()->get('id'), $this->get('managers'));
	}

	/**
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function member($user_id=null)
	{
		if (!isset($this->_member) 
		 || ($user_id !== null && (int) $this->_member->get('user_id') != $user_id))
		{
			$this->_member = null;

			/*if (!$user_id)
			{
				$user_id = JFactory::getUser()->get('id');
			}*/
		//$user_id = (int) $user_id;

		/*$this->_db->setQuery(
			"SELECT m.*, r.role, r.permissions AS role_permissions 
			FROM #__courses_offering_members AS m 
			LEFT JOIN #__courses_roles AS r ON r.id=m.role_id 
			WHERE m.`offering_id`=" . $this->_db->Quote($this->get('id')) . " AND m.`user_id`=" . $this->_db->Quote($id)
		);*/
			if (isset($this->_members) && isset($this->_members[$user_id]))
			{
				$this->_member = $this->_members[$user_id];
			}
		}

		if (!$this->_member)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php');

			$this->_member = CoursesModelMember::getInstance($user_id, $this->get('id'));
		}

		return $this->_member; 
	}

	/**
	 * Get a list of units for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function members($filters=array(), $clear=false)
	{
		//if (is_string($filters))
		if (isset($filters['count']) && $filters['count'])
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.php');

			$tbl = new CoursesTableMember($this->_db);

			$filters['offering_id'] = (int) $this->get('id');

			return $tbl->count($filters);
		}
		if (!isset($this->_members) || !is_array($this->_members) || $clear)
		{
			//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.php');

			$tbl = new CoursesTableMember($this->_db);

			$filters['offering_id'] = (int) $this->get('id');

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php');

				foreach ($data as $key => $result)
				{
					$results[$result->user_id] = new CoursesModelMember($result, $this->get('id'));
				}
			}

			$this->_members = $results; //new CoursesModelIterator($results);
		}

		return $this->_members;
	}

	/**
	 * Get a list of units for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function roles($filters=array(), $clear=false)
	{
		//if (is_string($filters))
		if (isset($filters['count']) && $filters['count'])
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'role.php');

			$tbl = new CoursesTableMember($this->_db);

			$filters['offering_id'] = (int) $this->get('id');

			return $tbl->count($filters);
		}
		if (!isset($this->_roles) || !is_array($this->_roles) || $clear)
		{
			//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'role.php');

			$tbl = new CoursesTableRole($this->_db);

			$filters['offering_id'] = array(0, (int) $this->get('id'));  // 0 = default roles

			if (!($results = $tbl->find($filters)))
			{
				/*require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php');

				foreach ($data as $key => $result)
				{
					$results[$result->user_id] = new CoursesModelMember($result, $this->get('id'));
				}*/
				$results = array();
			}

			$this->_roles = $results; //new CoursesModelIterator($results);
		}

		return $this->_roles;
	}

	/**
	 * Get a list of units for an offering
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function pages($idx=null)
	{
		if (!isset($this->pages) || !is_array($this->pages))
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'page.php');

			$tbl = new CoursesTablePage($this->_db);
			if (!($results = $tbl->find($this->get('id'), true)))
			{
				$results = array();
			}

			$this->pages = $results;
		}

		if ($idx !== null)
		{
			if (isset($this->pages[$idx]))
			{
				return $this->pages[$idx];
			}
			return null;
		}

		return $this->pages;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function plugins($idx=null)
	{
		if (!isset($this->_plugins) || !is_array($this->_plugins))
		{
			JPluginHelper::importPlugin('courses');
			$dispatcher =& JDispatcher::getInstance();

			$plugins = $dispatcher->trigger('onCourseAreas', array());

			array_unshift($plugins, array(
				'name'             => 'outline',
				'title'            => JText::_('Outline'),
				'default_access'   => 'members',
				'display_menu_tab' => true
			));

			$this->_plugins = $plugins;
		}

		if ($idx !== null)
		{
			return isset($this->_plugins[$idx]);
		}

		return $this->_plugins;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view', $item='offering')
	{
		if (!$this->_authorized)
		{
			// Set NOT viewable by default
			// We need to ensure the course is published first
			$this->params->set('access-view-offering', false);

			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				$this->_authorized = true;
			}
			else
			{
				// Anyone logged in can create a course
				//$this->params->set('access-create-offering', true);

				// Check if they're a site admin
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					if ($juser->authorize('com_courses', 'manage')) 
					{
						$this->params->set('access-view-offering', true);

						$this->params->set('access-admin-offering', true);
						$this->params->set('access-manage-offering', true);
						$this->params->set('access-create-offering', true);
						$this->params->set('access-delete-offering', true);
						$this->params->set('access-edit-offering', true);
						$this->params->set('access-edit-state-offering', true);
						$this->params->set('access-edit-own-offering', true);

						$this->params->set('access-admin-student', true);
						$this->params->set('access-manage-student', true);
						$this->params->set('access-create-student', true);
						$this->params->set('access-delete-student', true);
						$this->params->set('access-edit-student', true);
					}
				}
				else 
				{
					$this->params->set('access-view-offering', $juser->authorise('core.admin', $this->get('course_id')));
					$this->params->set('access-admin-offering', $juser->authorise('core.admin', $this->get('course_id')));
					$this->params->set('access-manage-offering', $juser->authorise('core.manage', $this->get('course_id')));
					$this->params->set('access-create-offering', $juser->authorise('core.admin', $this->get('course_id')));
					$this->params->set('access-delete-offering', $juser->authorise('core.manage', $this->get('course_id')));
					$this->params->set('access-edit-offering', $juser->authorise('core.manage', $this->get('course_id')));
					$this->params->set('access-edit-state-offering', $juser->authorise('core.manage', $this->get('course_id')));
					$this->params->set('access-edit-own-offering', $juser->authorise('core.manage', $this->get('course_id')));

					$this->params->set('access-admin-student', $juser->authorise('core.manage', $this->get('course_id')));
					$this->params->set('access-manage-student', $juser->authorise('core.manage', $this->get('course_id')));
					$this->params->set('access-create-student', $juser->authorise('core.manage', $this->get('course_id')));
					$this->params->set('access-delete-student', $juser->authorise('core.manage', $this->get('course_id')));
					$this->params->set('access-edit-student', $juser->authorise('core.manage', $this->get('course_id')));
				}

				// If they're not an admin
				if (!$this->params->get('access-admin-offering') 
				 && !$this->params->get('access-manage-offering'))
				{
					// If they're a course manager
					if ($this->params->get('access-manage-course'))
					{
						// Give full access
						$this->params->set('access-view-offering', true);
						$this->params->set('access-manage-offering', true);
						$this->params->set('access-create-offering', true);
						$this->params->set('access-delete-offering', true);
						$this->params->set('access-edit-offering', true);
						$this->params->set('access-edit-state-offering', true);
						$this->params->set('access-edit-own-offering', true);
	
						$this->params->set('access-admin-student', true);
						$this->params->set('access-manage-student', true);
						$this->params->set('access-create-student', true);
						$this->params->set('access-delete-student', true);
						$this->params->set('access-edit-student', true);
					}
					// Check if they're the offering creator
					else if ($this->get('created_by') == $juser->get('id'))
					{
						// Give full access
						$this->params->set('access-view-offering', true);
						$this->params->set('access-manage-offering', true);
						//$this->params->set('access-create-offering', true);  // Must be course manager to create offering
						$this->params->set('access-delete-offering', true);
						$this->params->set('access-edit-offering', true);
						$this->params->set('access-edit-state-offering', true);
						$this->params->set('access-edit-own-offering', true);
					}
					else
					{
						//$member = CoursesModelMember::getInstance($this->get('id'), $juser->get('id'));
						$this->params->merge($this->member($juser->get('id'))->access());
					}
					/*else if (in_array($juser->get('id'), $this->get('members')))
					{
						$this->params->set('access-view-offering', true);
					}*/
				}

				$this->_authorized = true;
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-' . $item);
	}

	/**
	 * Short description for 'getPluginAccess'
	 * Long description (if any) ...
	 *
	 * @param string $get_plugin Parameter description (if any) ...
	 * @return mixed Return description (if any) ...
	 */
	public function getPluginAccess($get_plugin = '')
	{
		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher = & JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		//then add overview to array
		$hub_course_plugins = $dispatcher->trigger('onCourseAreas', array());
		array_unshift($hub_course_plugins, array(
			'name' => 'outline', 
			'title' => 'Outline', 
			'default_access' => 'members'
		));

		//array to store plugin preferences when after retrieved from db
		$active_course_plugins = array();

		//get the course plugin preferences
		//returns array of tabs and their access level (ex. [overview] => 'anyone', [messages] => 'registered')
		$course_plugins = $this->get('plugins');

		/*$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		$course_plugins = new $paramsClass($this->get('plugins'));*/

		if ($course_plugins)
		{
			$course_plugins = explode(',', $course_plugins);

			foreach ($course_plugins as $plugin)
			{
				$temp = explode('=', trim($plugin));

				if ($temp[0])
				{
					$active_course_plugins[$temp[0]] = trim($temp[1]);
				}
			}
		}

		//array to store final course plugin preferences
		//array of acceptable access levels
		$course_plugin_access = array();
		$acceptable_levels = array('nobody', 'anyone', 'registered', 'members');

		//if we have already set some
		if ($active_course_plugins)
		{
			//for each plugin that is active on the hub
			foreach ($hub_course_plugins as $hgp)
			{
				//if course defined access level is not an acceptable value or not set use default value that is set per plugin
				//else use course defined access level
				if (!isset($active_course_plugins[$hgp['name']]) || !in_array($active_course_plugins[$hgp['name']], $acceptable_levels))
				{
					$value = $hgp['default_access'];
				}
				else
				{
					$value = $active_course_plugins[$hgp['name']];
				}

				//store final  access level in array of access levels
				$course_plugin_access[$hgp['name']] = $value;
			}
		}
		else
		{
			//for each plugin that is active on the hub
			foreach ($hub_course_plugins as $hgp)
			{
				$value = $hgp['default_access'];

				//store final  access level in array of access levels
				$course_plugin_access[$hgp['name']] = $value;
			}
		}

		//if we wanted to return only a specific level return that otherwise return all access levels
		if ($get_plugin != '')
		{
			return $course_plugin_access[$get_plugin];
		}
		else
		{
			return $course_plugin_access;
		}
	}
}

