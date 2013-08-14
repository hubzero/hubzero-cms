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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'manager.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'student.php');

/**
 * Courses model class for a course
 */
class CoursesModelPermissions extends JObject
{
	/**
	 * JUser
	 * 
	 * @var object
	 */
	private $_config = NULL;

	/**
	 * CoursesModelIterator
	 * 
	 * @var object
	 */
	private $_managers = NULL;

	/**
	 * CoursesModelMember
	 * 
	 * @var object
	 */
	private $_manager = NULL;

	/**
	 * CoursesModelIterator
	 * 
	 * @var object
	 */
	private $_students = NULL;

	/**
	 * CoursesModelMember
	 * 
	 * @var object
	 */
	private $_student = NULL;

	/**
	 * CoursesModelIterator
	 * 
	 * @var object
	 */
	private $_course_id = NULL;

	/**
	 * CoursesModelSection
	 * 
	 * @var object
	 */
	private $_offering_id = NULL;

	/**
	 * CoursesModelSection
	 * 
	 * @var object
	 */
	private $_section_id = NULL;

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($course_id=null, $offering_id=null, $section_id=null)
	{
		$this->set('course_id', $course_id);
		$this->set('offering_id', $offering_id);
		$this->set('section_id', $section_id);
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
	static function &getInstance($course_id=null, $offering_id=null, $section_id=null)
	{
		static $instance;

		if (!is_object($instance)) 
		{
			$instance = new CoursesModelPermissions($course_id, $offering_id, $section_id);
		}

		return $instance;
	}

	/**
	 * Check a user's authorization
	 * 
	 * @return     boolean True if authorized, false if not
	 */
	public function config()
	{
		if (!isset($this->_config))
		{
			$this->_config =& JComponentHelper::getParams('com_courses');
		}
		return $this->_config;
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
		if (isset($this->{'_' . $property})) 
		{
			return $this->{'_' . $property};
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
		$previous = isset($this->{'_' . $property}) ? $this->{'_' . $property} : null;
		$this->{'_' . $property} = $value;
		return $previous;
	}

	/**
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 * 
	 * @return     boolean
	 */
	public function isManager()
	{
		return $this->access('manage', 'section');
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
				$this->_manager = CoursesModelManager::getInstance($user_id, $this->get('course_id'), $this->get('offering_id'), $this->get('section_id'));
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
			$filters['course_id'] = (int) $this->get('course_id');
		}
		if (!isset($filters['offering_id']) && $this->get('offering_id') !== null)
		{
			$filters['offering_id'] = (int) $this->get('offering_id');
		}
		if (!isset($filters['section_id']) && $this->get('section_id') !== null)
		{
			$filters['section_id'] = (int) $this->get('section_id');
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
	 * Check if the current user has manager access
	 * This is just a shortcut for the access check
	 * 
	 * @return     boolean
	 */
	public function isStudent()
	{
		return $this->config()->get('is-student');
		/*if (!$this->access('manage', 'section') && $this->access('view', 'section'))
		{
			return true;
		}
		return false;*/
	}

	/**
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function student($user_id=null)
	{
		if (!isset($this->_student) 
		 || ($user_id !== null && (int) $this->_student->get('user_id') != $user_id))
		{
			$this->_student = null;

			if (isset($this->_students) && isset($this->_students[$user_id]))
			{
				$this->_student = $this->_students[$user_id];
			}
		}

		if (!$this->_student)
		{
			$this->_student = CoursesModelStudent::getInstance($user_id, $this->get('course_id'), null, $this->get('section_id'));
		}

		return $this->_student; 
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
	public function students($filters=array(), $clear=false)
	{
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('course_id');
		}
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('offering_id');
		}
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->get('section_id');
		}
		$filters['student'] = 1;

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableMember($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_students) || !is_array($this->_students) || $clear)
		{
			$tbl = new CoursesTableMember($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					$results[$result->user_id] = new CoursesModelStudent($result, $this->get('id'));
				}
			}

			$this->_students = $results; //new CoursesModelIterator($results);
		}

		return $this->_students;
	}

	/**
	 * Calculate permissions
	 * 
	 * @return     void
	 */
	private function _calculate()
	{
		$juser = JFactory::getUser();

		// Check if user is an admin, and set flag appropriately
		// Do this here (even though we also do it in courses.php), because not all calls (ex: api) go through courses.php
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$jacl = JFactory::getACL();
			$jacl->addACL('com_courses', 'manage', 'users', 'super administrator');
			$jacl->addACL('com_courses', 'manage', 'users', 'administrator');
			$jacl->addACL('com_courses', 'manage', 'users', 'manager');
		}

		// If they're not logged in
		/*if (!$juser->get('guest'))
		{*/
			// List of actions
			$actions = array(
				'admin', 'manage', 'create', 'delete', 'edit', 'edit-state', 'edit-own', 'view'
			);

			// Check if they're a site admin
			/*if (version_compare(JVERSION, '1.6', 'lt')) // Joomla 1.5.x
			{
				// If they're a site admin
				if ($juser->authorize('com_courses', 'manage')) 
				{
					// Authorize for each action and actionable item
					foreach ($actions as $action)
					{
						$this->config()->set('access-' . $action . '-course', true);
						$this->config()->set('access-' . $action . '-offering', true);
						$this->config()->set('access-' . $action . '-section', true);
					}
					$this->config()->set('access-checked-course', true);
					$this->config()->set('access-checked-offering', true);
					$this->config()->set('access-checked-section', true);
				}
			}
			else // Joomla 1.6+
			{
				// If they're a site admin
				if ($juser->authorise('core.manage', 'com_courses.component')) 
				{
					// Authorize for each action and actionable item
					foreach ($actions as $action)
					{
						$this->config()->set('access-' . $action . '-course', true);
						$this->config()->set('access-' . $action . '-offering', true);
						$this->config()->set('access-' . $action . '-section', true);
						//$this->config()->set('access-' . $action . '-student', true);
					}
					$this->config()->set('access-checked-course', true);
					$this->config()->set('access-checked-offering', true);
					$this->config()->set('access-checked-section', true);
				}
			}
		}*/

		// Are they an admin?
		if ($this->config()->get('access-admin-course') 
		 || $this->config()->get('access-manage-course'))
		{
			// Admin - no need to go any further
			return;
		}

		/*if (!$this->config()->get('access-checked-course'))
		{
			
		}*/
		// If no course Id found
		if (!$this->get('course_id'))
		{
			// Try to get the course from request
			$course = CoursesModelCourse::getInstance(JRequest::getVar('gid', ''));
			if ($course->exists())
			{
				$this->set('course_id', $course->get('id'));
			}
		}

		// Still no course? Can't do anything from here
		if ($this->get('course_id') === null)
		{
			return;
		}

		// Get the course
		if (!isset($course))
		{
			$course = CoursesModelCourse::getInstance($this->get('course_id'));
		}

		// Make sure the course exists
		if ($course->exists() && $course->isPublished())
		{
			$this->config()->set('access-view-course', true);
		}

		//$juser = JFactory::getUser();
		// If they're logged in
		if (!$juser->get('guest'))
		{
			$manager = $this->manager($juser->get('id'));
			// If they're NOT an admin
			if ($manager->exists())
			{
				// If no specific permissions set
				if ($manager->get('course_id') == $this->get('course_id') && !$manager->get('offering_id') && !$manager->get('section_id'))
				{
					if (!$manager->get('permissions'))
					{
						foreach ($actions as $action)
						{
							$this->config()->set('access-' . $action . '-section', true);
							$this->config()->set('access-' . $action . '-offering', true);
							$this->config()->set('access-' . $action . '-course', true);
						}
					}
					else
					{
						// Merge permissions
						$this->config()->merge($permissions);
					}

					$this->config()->set('is-manager', true);
					$this->config()->set('access-checked-offering', true);
					$this->config()->set('access-checked-section', true);
				}
			}
			// If they're a student
			else if (($student = $this->student($juser->get('id'))))
			{
				if ($student->exists())
				{
					// Allow them to view content
					$this->config()->set('is-student', true);
					$this->config()->set('access-view-course', true);
				}
			}
		}

		// Passed course level checks
		$this->config()->set('access-checked-course', true);

		// If no offering ID is found
		if (!$this->get('offering_id'))
		{
			$oid = JRequest::getVar('offering', '');

			//$course = CoursesModelCourse::getInstance($this->get('course_id'));
			$offering = $course->offering($oid);
			if ($offering->exists())
			{
				$this->set('offering_id', $offering->get('id'));
				$this->set('section_id', $offering->section()->get('id'));
			}
		}

		// Ensure we have an offering to work with
		if ($this->get('offering_id') === null)
		{
			return;
		}

		if (!isset($offering))
		{
			$course = CoursesModelCourse::getInstance($this->get('course_id'));
			$offering = $course->offering($this->get('offering_id'));
		}

		// Offering isn't available
		if (!$offering->exists() || !$offering->isPublished())
		{
			return;
		}

		// If they're logged in
		if (!$juser->get('guest'))
		{
			$manager = $this->manager($juser->get('id'));
			if ($manager->exists())
			{
				// If no specific permissions set
				if ($manager->get('course_id') == $this->get('course_id') && $manager->get('offering_id') == $this->get('offering_id'))
				{
					if (!$manager->get('permissions'))
					{
						// If section_id is set, then they're a section manager
						if ($manager->get('section_id') == $this->get('section_id'))
						{
							foreach ($actions as $action)
							{
								$this->config()->set('access-' . $action . '-section', true);
							}
						}
						else 
						{
							foreach ($actions as $action)
							{
								$this->config()->set('access-' . $action . '-section', true);
								$this->config()->set('access-' . $action . '-offering', true);
								//$this->config()->set('access-' . $action . '-course', true);
							}
						}
					}
					else
					{
						// Merge permissions
						$this->config()->merge($permissions);
					}
					$this->config()->set('access-view-offering', true);
					$this->config()->set('access-view-section', true);
				}
			}
			// If they're a student
			else if (($student = $this->student($juser->get('id'))))
			{
				if ($student->exists())
				{
					// Allow them to view content
					$this->config()->set('access-view-offering', true);

					// Give section view privileges if in identified section
					if ($student->get('section_id') == $this->get('section_id'))
					{
						$this->config()->set('access-view-section', true);
					}
				}
			}
		}

		$this->config()->set('access-checked-offering', true);
		$this->config()->set('access-checked-section', true);
	}

	/**
	 * Check a user's authorization
	 * 
	 * @param      string $action Action to check
	 * @return     boolean True if authorized, false if not
	 */
	public function access($action='view', $item='course')
	{
		if (!$this->config()->get('access-checked-' . $item))
		{
			$this->_calculate();
		}

		return $this->config()->get('access-' . strtolower($action) . '-' . $item);
	}
}

