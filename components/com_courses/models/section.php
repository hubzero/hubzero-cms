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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'section.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'sectiondate.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php');

/**
 * Courses model class for a course
 */
class CoursesModelSection extends JObject
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
	private $_tbl = NULL;

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
	private $_creator = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($oid, $offering_id=null)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableSection($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->_tbl->load($oid, $offering_id);
		}
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
		}

		//$this->params = JComponentHelper::getParams('com_courses');
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
	static function &getInstance($oid=null, $offering_id=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		$key = 0;

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $oid . '_' . $offering_id;
		}
		else if (is_object($oid))
		{
			$key = $oid->get('id') . '_' . $offering_id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'] . '_' . $offering_id;
		}

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new CoursesModelSection($oid, $offering_id);
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
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 * 
	 * @return     boolean
	 */
	public function isMember($id=null)
	{
		if (!$id)
		{
			$juser = JFactory::getUser();
			$id = $juser->get('id');
		}
		return $this->member($id)->exists();
	}

	/**
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 * 
	 * @return     boolean
	 */
	public function isManager()
	{
		return $this->access('manage');
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
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->get('id');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableMember($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_members) || !is_array($this->_members) || $clear)
		{
			//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');

			$tbl = new CoursesTableMember($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
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
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function date($scope=null, $scope_id=null)
	{
		if (!isset($this->_date) 
		 || ((int) $this->_date->get('scope_id') != $scope_id && (string) $this->_date->get('scope') != $scope))
		{
			$this->_date = new CoursesModelSectionDate();

			foreach ($this->dates() as $dt)
			{
				if ($dt->get('scope') == $scope
				 && $dt->get('scope_id') == $scope_id)
				{
					$this->_date = $dt;
				}
			}
		}

		return $this->_date; 
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
	public function dates($filters=array(), $clear=false)
	{
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->get('id');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableSectionDate($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_dates) || !is_array($this->_dates) || $clear)
		{
			$tbl = new CoursesTableSectionDate($this->_db);

			if (($results = $tbl->find($filters)))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelSectionDate($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_dates = new CoursesModelIterator($results);
		}

		return $this->_dates;
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
	 * Add one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function add($data = array(), $role_id=0)
	{
		foreach ($data as $result)
		{
			$user_id = $this->_userId($result);

			// Create the entry
			$model = new CoursesModelMember($result, $this->get('id'));
			$model->set('role_id', $role_id);
			if (!$model->store())
			{
				$this->setError($model->getError());
				continue;
			}

			// Append to the members list
			if (isset($this->_members) && is_array($this->_members))
			{
				$this->_members[$user_id] = $model;
			}
		}
	}

	/**
	 * Remove one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function remove($data = array())
	{
		foreach ($data as $result)
		{
			$user_id = $this->_userId($result);

			$model = CoursesModelMember::getInstance($user_id, $this->get('id'));
			if (!$model->delete())
			{
				$this->setError($model->getError());
				continue;
			}

			if (isset($this->_members[$user_id]))
			{
				unset($this->_members[$user_id]);
			}
		}
	}

	/**
	 * Return an ID for a user
	 *
	 * @param     mixed $user User ID or username
	 * @return    integer
	 */
	private function _userId($user)
	{
		if (is_numeric($user))
		{
			return $user;
		}

		$this->_db->setQuery("SELECT id FROM #__users WHERE username=" . $this->_db->Quote($user));

		if (($result = $this->_db->loadResult()))
		{
			return $result;
		}

		return 0;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function store($check=true)
	{
		if (empty($this->_db))
		{
			return false;
		}

		$isNew = ($this->get('id') ? false : true);

		if ($check)
		{
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		JPluginHelper::importPlugin('courses');

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onAfterSaveSection', array($this, $isNew));

		if ($isNew)
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'log.php');

			$log = new CoursesTableLog($this->database);
			$log->scope_id  = $this->get('id');
			$log->scope     = 'section';
			$log->user_id   = $juser->get('id');
			$log->timestamp = date('Y-m-d H:i:s', time());
			$log->action    = 'created';
			//$log->comments  = $log;
			$log->actor_id  = $juser->get('id');
			if (!$log->store()) 
			{
				$this->setError($log->getError());
			}
		}

		return true;
	}

	/**
	 * Store changes to this offering
	 *
	 * @param     boolean $check Perform data validation check?
	 * @return    boolean False if error, True on success
	 */
	public function delete()
	{
		// Remove associated date data
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'section.date.php');

		$sd = new CoursesTableSectionDate($this->_db);
		if (!$sd->deleteBySection($this->get('id')))
		{
			$this->setError($sd->getError());
			return false;
		}

		// Remove associated member data
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'member.php');

		$sm = new CoursesTableMember($this->_db);
		if (!$sm->deleteBySection($this->get('id')))
		{
			$this->setError($sm->getError());
			return false;
		}

		// Get some data for the log
		$log = json_encode($this->_tbl);

		$scope_id = $this->get('id');

		// Remove section
		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		JPluginHelper::importPlugin('courses');

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onAfterDeleteSection', array($this));

		// Log the event
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'log.php');

		$juser = JFactory::getUser();

		$log = new CoursesTableLog($this->_db);
		$log->scope_id  = $scope_id;
		$log->scope     = 'section';
		$log->user_id   = $juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action    = 'deleted';
		$log->comments  = $log;
		$log->actor_id  = $juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		return true;
	}
}

