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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'permissions.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'page.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'tags.php');

/**
 * Courses model class for a course
 */
class CoursesModelCourse extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableCourse';

	/**
	 * Object scope
	 * 
	 * @var string
	 */
	protected $_scope = 'course';

	/**
	 * CoursesModelOffering
	 * 
	 * @var object
	 */
	private $_offering = NULL;

	/**
	 * CoursesModelIterator
	 * 
	 * @var object
	 */
	private $_offerings = NULL;

	/**
	 * CoursesModelPermissions
	 * 
	 * @var object
	 */
	private $_permissions = NULL;

	/**
	 * CoursesModelOffering
	 * 
	 * @var object
	 */
	private $_manager = NULL;

	/**
	 * CoursesModelIterator
	 * 
	 * @var object
	 */
	private $_managers = NULL;

	/**
	 * CoursesModelOffering
	 * 
	 * @var object
	 */
	private $_page = NULL;

	/**
	 * CoursesModelIterator
	 * 
	 * @var object
	 */
	private $_pages = NULL;

	/**
	 * List of plugins available for a given event
	 * 
	 * @var array
	 */
	private $_plugins = array();

	/**
	 * URL to this object
	 * 
	 * @var string
	 */
	private $_link = NULL;

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course ID or alias
	 * @return     void
	 */
	public function __construct($oid)
	{
		parent::__construct($oid);

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$this->config()->merge(new $paramsClass($this->get('params')));

		if (!isset($this->_permissions))
		{
			$this->_permissions = CoursesModelPermissions::getInstance();
			$this->_permissions->set('course_id', $this->get('id'));
		}
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
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 * 
	 * @return     boolean
	 */
	public function isManager($user_id=0)
	{
		if ((int) $user_id)
		{
			$this->_permissions->isManager($user_id);
		}
		return $this->_permissions->isManager();
	}

	/**
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 * 
	 * @return     boolean
	 */
	public function isStudent($user_id=0)
	{
		if ((int) $user_id)
		{
			$this->_permissions->isStudent($user_id);
		}
		return $this->_permissions->isStudent();
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
		if (!isset($this->_offering) 
		 || ($id !== null && (int) $this->_offering->get('id') != $id && (string) $this->_offering->get('alias') != $id))
		{
			// Reset current offering
			$this->_offering = null;

			// If the list of all offerings is available ...
			if (isset($this->_offerings))
			{
				// Find an offering in the list that matches the ID passed
				foreach ($this->offerings() as $key => $offering)
				{
					if ((int) $offering->get('id') == $id || (string) $offering->get('alias') == $id)
					{
						// Set current offering
						$this->_offering = $offering;
						break;
					}
				}
			}

			if (is_null($this->_offering))
			{
				// Get current offering
				$this->_offering = CoursesModelOffering::getInstance($id, $this->get('id'));
			}
		}
		// Return current offering
		return $this->_offering;
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
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('id');
		}

		// Perform a record count?
		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableOffering($this->_db);

			return $tbl->count($filters);
		}

		// Is the data is not set OR is it not the right type?
		if (!isset($this->_offerings) || !is_a($this->_offerings, 'CoursesModelIterator'))
		{
			$tbl = new CoursesTableOffering($this->_db);

			// Attempt to get database results
			if (($results = $tbl->find($filters)))
			{
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
			$this->_offerings = new CoursesModelIterator($results);
		}

		// Return results
		return $this->_offerings;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view', $item='course')
	{
		return $this->_permissions->access($action, $item);
	}

	/**
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function manager($user_id=null)
	{
		if (!isset($this->_manager) 
		 || ($user_id !== null && (int) $this->_manager->get('user_id') != $user_id))
		{
			$this->_manager = null;

			if (isset($this->_managers) && isset($this->_managers[$user_id]))
			{
				$this->_manager = $this->_managers[$user_id];
			}

			if (!$this->_manager)
			{
				$this->_manager = CoursesModelManager::getInstance($user_id, $this->get('course_id'), 0, 0);
			}
		}

		return $this->_manager;
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
	public function managers($filters=array(), $clear=false)
	{
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('id');
		}
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = 0;
		}
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = 0;
		}
		$filters['student'] = 0;

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableMember($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_managers) || !is_array($this->_managers) || $clear)
		{
			$tbl = new CoursesTableMember($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					if (!isset($results[$result->user_id]))
					{
						$results[$result->user_id] = new CoursesModelManager($result, $this->get('id'));
					}
					else
					{
						// Course manager takes precedence over offering manager
						if ($result->course_id && !$result->offering_id && !$result->section_id)
						{
							$results[$result->user_id] = new CoursesModelManager($result, $this->get('id'));
						}
						// Course offering takes precedence over section manager
						else if ($result->course_id && $result->offering_id && !$result->section_id)
						{
							$results[$result->user_id] = new CoursesModelManager($result, $this->get('id'));
						}
					}
				}
			}

			$this->_managers = $results; //new CoursesModelIterator($results);
		}

		return $this->_managers;
	}

	/**
	 * Get a list of instructors for a course
	 * 
	 * @param      array   $filters Filters to build query from
	 * @param      boolean $clear   Force a new dataset?
	 * @return     mixed
	 */
	public function instructors($filters=array(), $clear=true)
	{
		$filters['role'] = 'instructor';
		return $this->managers($filters, $clear);
	}

	/**
	 * Add one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function add($data = null, $role_id=0)
	{
		$user_ids = $this->_userIds($data);

		$tbl = new CoursesTableMember($this->_db);

		$filters = array(
			'course_id' => (int) $this->get('id')
		);

		foreach ($user_ids as $user_id)
		{
			$filters['user_id'] = $user_id;

			if (($data = $tbl->find($filters)))
			{
				$this->_managers[$user_id] = new CoursesModelManager(array_shift($data), $this->get('id'));

				if (count($data) > 0)
				{
					foreach ($data as $key => $result)
					{
						$tbl->delete($result->id);
						//$data[$key] = new CoursesModelManager($result, $this->get('id'));
						//$data[$key]->delete();
					}
				}
			}

			if (!isset($this->_managers[$user_id]))
			{
				$this->_managers[$user_id] = new CoursesModelManager($user_id, $this->get('id'));
			}
			$this->_managers[$user_id]->set('user_id', $user_id);
			$this->_managers[$user_id]->set('course_id', $this->get('id'));
			$this->_managers[$user_id]->set('role_id', $role_id);
			$this->_managers[$user_id]->set('section_id', 0);
			$this->_managers[$user_id]->set('student', 0);
			$this->_managers[$user_id]->set('offering_id', 0);
			$this->_managers[$user_id]->store();
		}
	}

	/**
	 * Remove one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function remove($data = null)
	{
		$user_ids = $this->_userIds($data);

		if (count($user_ids) > 0)
		{
			$this->managers();

			foreach ($user_ids as $user_id)
			{
				if (isset($this->_managers[$user_id]))
				{
					$this->_managers[$user_id]->delete();
					unset($this->_managers[$user_id]);
				}
			}
		}
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

		$isNew = ($this->get('id') ? false : true);

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

		/*foreach (self::$_list_keys as $property)
		{
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
					$query = "DELETE FROM $aux_table WHERE course_id=" . $this->_db->Quote($this->get('id')) . ";";
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
		}*/

		// After SQL is done and has no errors, fire off onCourseUserEnrolledEvents 
		// for every user added to this course
		JPluginHelper::importPlugin('courses');

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onCourseSave', array($this));

		if ($affected > 0)
		{
			JPluginHelper::importPlugin('user');

			// trigger the onAfterStoreCourse event
			$dispatcher->trigger('onAfterStoreCourse', array($this));
		}

		if ($isNew)
		{
			$this->log($this->get('id'), $this->_scope, 'create');
		}

		return true;
	}

	/**
	 * Delete an entry and associated data
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		$value = parent::delete();

		JPluginHelper::importPlugin('courses');
		JDispatcher::getInstance()->trigger('onCourseDelete', array($this));

		return $value;
	}

	/**
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function page($url=null)
	{
		if (!isset($this->_page) 
		 || ($url !== null && (string) $this->_page->get('url') != $url))
		{
			$this->_page = null;

			if (isset($this->_pages) && is_array($this->_pages) && isset($this->_pages[$url]))
			{
				$this->_page = $this->_pages[$url];
			}

			if (!$this->_page)
			{
				$this->_page = new CoursesModelPage(0);
			}
		}

		return $this->_page; 
	}

	/**
	 * Get a list of pages for a course
	 * 
	 * @param      array $filters Filters to apply
	 * @return     array
	 */
	public function pages($filters=array())
	{
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('id');
		}
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = 0;
		}
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'ordering';
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTablePage($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_pages) || !is_array($this->_pages))
		{
			$tbl = new CoursesTablePage($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					$results[$result->url] = new CoursesModelPage($result);
				}
			}

			$this->_pages = $results;
		}

		return $this->_pages;
	}

	/**
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function tags($what='cloud')
	{
		$ct = new CoursesTags($this->_db);

		$tags = null;

		$what = strtolower(trim($what));
		switch ($what)
		{
			case 'array':
				$tags = $ct->get_tags_on_object($this->get('id'), 0, 0);
			break;

			case 'string':
				$tags = $ct->get_tag_string($this->get('id'));
			break;

			case 'cloud':
				$tags = $ct->get_tag_cloud(0, 0, $this->get('id'));
			break;
		}

		return $tags; 
	}

	/**
	 * Get a list of plugins available for a given event
	 * 
	 * @return     array
	 */
	/*public function plugins($event='onCourseViewAreas')
	{
		if (!isset($this->_plugins[$event]))
		{
			JPluginHelper::importPlugin('courses');
			$dispatcher =& JDispatcher::getInstance();

			$this->_plugins[$event] = $dispatcher->trigger($event, array(
					$this
				)
			);
		}
		return $this->_plugins[$event];
	}*/

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 * 
	 * @param      string $type The type of link to return
	 * @return     boolean
	 */
	public function link($type='')
	{
		if (!isset($this->_link))
		{
			$this->_link  = 'index.php?option=com_courses&gid=' . $this->get('alias');
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link = $this->_base . '&task=edit';
			break;

			case 'delete':
				$link = $this->_base . '&task=delete';
			break;

			case 'permalink':
			default:
				$link = $this->_base;
			break;
		}

		return $link;
	}
}

