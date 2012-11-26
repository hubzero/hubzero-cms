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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'course.php');

/**
 * Courses model class for a course
 */
class CoursesModelCourse extends JObject
{
	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_tbl = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	public $offering = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	public $offerings = NULL;

	/**
	 * JUser
	 * 
	 * @var object
	 */
	public $params = NULL;

	/**
	 * Flag for if authorization checks have been run
	 * 
	 * @var mixed
	 */
	private $_authorized = false;

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
	 * Description for '_list_keys'
	 *
	 * @var array
	 */
	static $_list_keys = array('managers');

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableCourse($this->_db);

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

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$this->params = JComponentHelper::getParams('com_courses');
		$this->params->merge(new $paramsClass($this->get('params')));

		//$this->params = new $paramsClass($this->course->get('params'));
	}

	/**
	 * Returns a reference to a course model
	 *
	 * This method must be invoked as:
	 *     $offering = CoursesModelCourse::getInstance($alias);
	 *
	 * @param      mixed $oid Course ID (int) or alias (string)
	 * @return     object CoursesModelCourse
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$oid])) 
		{
			$instances[$oid] = new CoursesModelCourse($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param     string $property The name of the property
	 * @param     mixed  $default  The default value
	 * @return    mixed The value of the property
 	 */
	public function get($property, $default=null)
	{
		if (in_array($property, self::$_list_keys))
		{
			if (!array_key_exists($property, get_object_vars($this->_tbl)))
			{
				$this->_tbl->$property = array();

				if (is_object($this->_db))
				{
					$this->_db->setQuery("SELECT user_id from #__courses_managers WHERE course_id=" . $this->_db->Quote($this->_tbl->get('id')));

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
	 * @param     string $property The name of the property
	 * @param     mixed  $value    The value of the property to set
	 * @return    mixed Previous value of the property
	 */
	public function set($property, $value = null)
	{
		$previous = isset($this->_tbl->$property) ? $this->_tbl->$property : null;
		$this->_tbl->$property = $value;
		return $previous;
	}

	/**
	 * Check if the course exists
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
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 * 
	 * @return     boolean
	 */
	public function isManager($user_id=0)
	{
		/*require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'manager.php');

		$tbl = new CoursesTableManager($this->_db);

		return $tbl->find($this->get('id'), $user_id); //in_array(JFactory::getUser()->get('id'), $this->get('managers'));*/
		if ((int) $user_id)
		{
			// Check if we've already grabbed the list of managers
			// This will save us a query if so
			if (isset($this->_tbl->managers))
			{
				return in_array($user_id, $this->get('managers'));
			}
			else
			{
				// See if a manager record exist for this user
				$this->_db->setQuery("SELECT user_id from #__courses_managers WHERE course_id=" . $this->_db->Quote($this->get('id')) . " AND user_id=" . $this->_db->Quote($user_id));

				if (($result = $this->_db->loadResult()))
				{
					return true;
				}
			}
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
	 * Set and get a specific offering
	 * 
	 * @return     void
	 */
	public function offering($id=null)
	{
		// If the current offering isn't set
		//    OR the ID passed doesn't equal the current offering's ID or alias
		if (!isset($this->offering) 
		 || ($id !== null && (int) $this->offering->get('id') != $id && (string) $this->offering->get('alias') != $id))
		{
			// Reset current offering
			$this->offering = null;

			// If the list of all offerings is available ...
			if (isset($this->offerings))
			{
				// Find an offering in the list that matches the ID passed
				foreach ($this->offerings() as $key => $offering)
				{
					if ((int) $offering->get('id') == $id || (string) $offering->get('alias') == $id)
					{
						// Set current offering
						$this->offering = $offering;
						break;
					}
				}
			}
			else
			{
				// Get current offering
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');

				$this->offering = CoursesModelOffering::getInstance($id);
			}
		}
		// Return current offering
		return $this->offering;
	}

	/**
	 * Get a list of offerings for a course
	 *   Accepts an array of filters to build query from
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     mixed
	 */
	public function offerings($filters=array())
	{
		if (isset($filters['count']) && $filters['count'])
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'offering.php');

			$tbl = new CoursesTableOffering($this->_db);

			$filters['course_id'] = (int) $this->get('id');

			return $tbl->count($filters);
		}
		// Is the data is not set OR is it not the right type?
		if (!isset($this->offerings) || !is_a($this->offerings, 'CoursesModelIterator'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'offering.php');

			$tbl = new CoursesTableOffering($this->_db);

			// Set the course ID
			$filters['course_id'] = (int) $this->get('id');

			// Attempt to get database results
			if (($results = $tbl->find($filters)))
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');

				// Loop through each result and turn into a model object
				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelOffering($result);
				}
			}
			else
			{
				// No results found
				// We need an empty array for the Iterator model
				$results = array();
			}

			// Set the results
			$this->offerings = new CoursesModelIterator($results);
		}

		// Return results
		return $this->offerings;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view')
	{
		if (!$this->_authorized)
		{
			// Set NOT viewable by default
			// We need to ensure the course is published first
			$this->params->set('access-view-course', false);

			// Does it exist and is it published?
			if ($this->exists() && $this->get('state') == 1)
			{
				$this->params->set('access-view-course', true);
			}

			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				// Not logged in. Can't go any further.
				$this->_authorized = true;
			}
			else
			{
				// Anyone logged in can create a course
				$this->params->set('access-create-course', true);

				// Check if they're a site admin
				if (version_compare(JVERSION, '1.6', 'lt'))  // Joomla 1.5
				{
					if ($juser->authorize('com_courses', 'manage')) 
					{
						// Admins get full access
						$this->params->set('access-admin-course', true);
						$this->params->set('access-manage-course', true);
						$this->params->set('access-delete-course', true);
						$this->params->set('access-edit-course', true);
						$this->params->set('access-edit-state-course', true);
						$this->params->set('access-edit-own-course', true);
					}
				}
				else  // Joomla 1.6+
				{
					$this->params->set('access-admin-course', $juser->authorise('core.admin', $this->get('id')));
					$this->params->set('access-manage-course', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-delete-course', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-course', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-state-course', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-own-course', $juser->authorise('core.manage', $this->get('id')));
				}

				// If they're not an admin
				if (!$this->params->get('access-admin-course')    // Can't admin
				 && !$this->params->get('access-manage-course'))  // Can't manage
				{
					// Does the course exist?
					if (!$this->exists())
					{
						// Give editing access if the course doesn't exist
						// i.e., it's a new course
						$this->params->set('access-view-course', false);    // Can't view what doesn't exist
						$this->params->set('access-delete-course', false);  // Likewise, can't delete what doesn't exist
						$this->params->set('access-edit-course', true);     // Ah, but we need edit access for the creation form
						$this->params->set('access-edit-state-course', true);
						$this->params->set('access-edit-own-course', true);
					}
					// Check if they're the course creator or course manager
					else if ($this->get('created_by') == $juser->get('id')  // Course creator
						  || $this->isManager($juser->get('id')))           // Course manager //in_array($juser->get('id'), $this->get('managers'))) 
					{
						// Give full access
						$this->params->set('access-manage-course', true);
						$this->params->set('access-delete-course', true);
						$this->params->set('access-edit-course', true);
						$this->params->set('access-edit-state-course', true);
						$this->params->set('access-edit-own-course', true);
					}
				}

				$this->_authorized = true;
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-course');
	}

	/**
	 * Add one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function add($value = array())
	{
		$users = $this->_userIds($value);

		$this->set('managers', array_merge($this->get('managers'), $users));
	}

	/**
	 * Remove one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function remove($value = array())
	{
		$users = $this->_userIds($value);

		$this->set('managers', array_diff($this->get('managers'), $users));
	}

	/**
	 * Return a list of user IDs for a list of usernames
	 *
	 * @param     array $users List of user IDs or usernames
	 * @return    array List of user IDs
	 */
	private function _userIds($users)
	{
		if (empty($this->_db))
		{
			return false;
		}

		$usernames = array();
		$userids = array();

		if (!is_array($users))
		{
			$users = array($users);
		}

		foreach ($users as $u)
		{
			if (is_numeric($u))
			{
				$userids[] = $u;
			}
			else
			{
				$usernames[] = $this->_db->Quote($u);
			}
		}

		if (empty($usernames))
		{
			return $userids;
		}

		$this->_db->setQuery("SELECT id FROM #__users WHERE username IN (" . implode($usernames, ",") . ");");

		if (!($result = $this->_db->loadResultArray()))
		{
			$result = array();
		}

		return array_merge($result, $userids);
	}

	/**
	 * Short title for 'update'
	 * Long title (if any) ...
	 *
	 * @param unknown $course_id Parameter title (if any) ...
	 * @param array $data Parameter title (if any) ...
	 * @return boolean Return title (if any) ...
	 */
	public function store($check=true)
	{
		if (empty($this->_db))
		{
			return false;
		}

		$first = true;

		$affected = 0;

		$aNewUserCourseEnrollments = array();

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

		$affected = $this->_db->getAffectedRows();

		foreach (self::$_list_keys as $property)
		{
			/*if (!in_array($property, $this->_updatedkeys))
			{
				continue;
			}*/
			$query = '';

			$aux_table = "#__courses_" . $property;

			$list = $this->get($property);

			if (!is_null($list) && !is_array($list))
			{
				$list = array($list);
			}

			$ulist = null;
			$tlist = null;

			foreach ($list as $value)
			{
				if (!is_null($ulist))
				{
					$ulist .= ',';
					$tlist .= ',';
				}

				$ulist .= $this->_db->Quote($value);
				$tlist .= '(' . $this->_db->Quote($this->get('id')) . ',' . $this->_db->Quote($value) . ')';
			}

			// @FIXME: I don't have a better solution yet. But the next refactoring of this class
			// should eliminate the ability to read the entire member table due to problems with
			// scale on a large (thousands of members) courses. The add function should track the members
			// being added to a course, but would need to be verified to handle adding members
			// already in course. *njk*

			// @FIXME: Not neat, but because all course membership is resaved every time even for single additions
			// there is no nice way to detect only *new* additions without this check. I don't want to 
			// fire off an 'onUserCourseEnrollment' event for users unless they are really being enrolled. *drb*

			if (in_array($property, array('managers')))
			{
				$query = "SELECT user_id FROM #__courses_$property WHERE course_id=" . $this->get('id');
				$this->_db->setQuery($query);

				// compile current list of members in this course
				$aExistingUserMembership = array();

				if (($results = $this->_db->loadAssoc()))
				{
					foreach ($results as $uid)
					{
						$aExistingUserMembership[] = $uid;
					}
				}

				// see who is missing
				$aNewUserCourseEnrollments = array_diff($list, $aExistingUserMembership);
			}

			if (is_array($list) && count($list) > 0)
			{
				if (in_array($property, array('managers')))
				{
					$query = "REPLACE INTO $aux_table (course_id, user_id) VALUES $tlist;";

					$this->_db->setQuery($query);

					if ($this->_db->query())
					{
						$affected += $this->_db->getAffectedRows();
					}
				}
			}

			if (!is_array($list) || count($list) == 0)
			{
				if (in_array($property, array('managers')))
				{
					$query = "DELETE FROM $aux_table WHERE course_id=" . $this->_db->Quote($this->id) . ";";
				}
			}
			else
			{
				if (in_array($property, array('managers')))
				{
					$query = "DELETE m FROM #__courses_$property AS m WHERE " . " m.course_id=" . 
						$this->_db->Quote($this->get('id')) . " AND m.user_id NOT IN (" . $ulist . ");";
				}
			}

			if ($query)
			{
				$this->_db->setQuery($query);

				if ($this->_db->query())
				{
					$affected += $this->_db->getAffectedRows();
				}
			}
		}

		// After SQL is done and has no errors, fire off onCourseUserEnrolledEvents 
		// for every user added to this course
		JPluginHelper::importPlugin('courses');
		$dispatcher = & JDispatcher::getInstance();

		/*foreach ($aNewUserCourseEnrollments as $userid)
		{
			$dispatcher->trigger('onCourseUserEnrollment', array($this->get('id'), $userid));
		}*/

		if ($affected > 0)
		{
			JPluginHelper::importPlugin('user');

			//trigger the onAfterStoreCourse event
			$dispatcher =& JDispatcher::getInstance();
			$dispatcher->trigger('onAfterStoreCourse', array($this));
		}

		/*$log = new CoursesTableLog($this->database);
		$log->gid       = $this->get('id');
		$log->uid       = $juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action    = 'course_deleted';
		$log->comments  = $log;
		$log->actorid   = $juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}*/

		return true;
	}
}

