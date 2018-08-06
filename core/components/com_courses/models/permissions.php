<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Hubzero\Base\Obj;
use Component;
use Request;
use User;

require_once __DIR__ . DS . 'course.php';
require_once __DIR__ . DS . 'manager.php';
require_once __DIR__ . DS . 'student.php';

/**
 * Courses model class for course permissions
 */
class Permissions extends Obj
{
	/**
	 * Config
	 *
	 * @var object
	 */
	private $_config = null;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	private $_managers = null;

	/**
	 * \Components\Courses\Models\Member
	 *
	 * @var object
	 */
	private $_manager = null;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	private $_students = null;

	/**
	 * \Components\Courses\Models\Member
	 *
	 * @var object
	 */
	private $_student = null;

	/**
	 * \Components\Courses\Models\Iterator
	 *
	 * @var object
	 */
	private $_course_id = null;

	/**
	 * \Components\Courses\Models\Section
	 *
	 * @var object
	 */
	private $_offering_id = null;

	/**
	 * \Components\Courses\Models\Section
	 *
	 * @var object
	 */
	private $_section_id = null;

	/**
	 * Constructor
	 *
	 * @param   integer $course_id   Course ID or alias
	 * @param   integer $offering_id Course offering ID or alias
	 * @param   integer $section_id  Course section ID or alias
	 * @return  void
	 */
	public function __construct($course_id=null, $offering_id=null, $section_id=null)
	{
		$this->set('course_id', $course_id);
		$this->set('offering_id', $offering_id);
		$this->set('section_id', $section_id);
	}

	/**
	 * Returns a reference to a permissions model
	 *
	 * @param   integer $course_id   Course ID or alias
	 * @param   integer $offering_id Course offering ID or alias
	 * @param   integer $section_id  Course section ID or alias
	 * @return  object \Components\Courses\Models\Permissions
	 */
	static function &getInstance($course_id=null, $offering_id=null, $section_id=null)
	{
		static $instance;

		if (!is_object($instance))
		{
			$instance = new Permissions($course_id, $offering_id, $section_id);
		}

		return $instance;
	}

	/**
	 * Get the component config
	 *
	 * @return  object Registry
	 */
	public function config()
	{
		if (!isset($this->_config))
		{
			$this->_config = Component::params('com_courses');
		}
		return $this->_config;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string $property The name of the property
	 * @param   mixed  $default  The default value
	 * @return  mixed  The value of the property
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
	 * @param   string $property The name of the property
	 * @param   mixed  $value The value of the property to set
	 * @return  mixed  Previous value of the property
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
	 * @return  boolean
	 */
	public function isManager()
	{
		return $this->access('manage', 'section');
	}

	/**
	 * Check if the current user is enrolled
	 *
	 * @param   integer $user_ID
	 * @return  boolean
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
				$this->_manager = Manager::getInstance($user_id, $this->get('course_id'), $this->get('offering_id'), $this->get('section_id'));
			}
		}

		return $this->_manager;
	}

	/**
	 * Get a list of managers
	 *
	 * @param   array   $filters Filters to build query from
	 * @param   boolean $clear   Force a new dataset?
	 * @return  mixed
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
			$tbl = new Tables\Member($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_managers) || !is_array($this->_managers) || $clear)
		{
			$tbl = new Tables\Member($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					if (!isset($results[$result->user_id]))
					{
						$results[$result->user_id] = new Manager($result, $this->get('id'));
					}
					else
					{
						// Course manager takes precedence over offering manager
						if ($result->course_id && !$result->offering_id && !$result->section_id)
						{
							$results[$result->user_id] = new Manager($result, $this->get('id'));
						}
						// Course offering takes precedence over section manager
						else if ($result->course_id && $result->offering_id && !$result->section_id)
						{
							$results[$result->user_id] = new Manager($result, $this->get('id'));
						}
					}
				}
			}

			$this->_managers = $results;
		}

		return $this->_managers;
	}

	/**
	 * Check if the current user is a student
	 * This is just a shortcut for the access check
	 *
	 * @return  boolean
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
	 * @param   integer $user_id
	 * @return  boolean
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
			$this->_student = \Components\Courses\Models\Student::getInstance($user_id, $this->get('course_id'), null, $this->get('section_id'));
		}

		return $this->_student;
	}

	/**
	 * Get a list of students
	 *
	 * @param   array   $filters Filters to build query from
	 * @param   boolean $clear   Force a new dataset?
	 * @return  mixed
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
			$tbl = new Tables\Member($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_students) || !is_array($this->_students) || $clear)
		{
			$tbl = new Tables\Member($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					$results[$result->user_id] = new \Components\Courses\Models\Student($result, $this->get('id'));
				}
			}

			$this->_students = $results; //new \Components\Courses\Models\Iterator($results);
		}

		return $this->_students;
	}

	/**
	 * Calculate permissions
	 *
	 * @return  void
	 */
	private function _calculate()
	{
		// List of actions
		$actions = array(
			'admin', 'manage', 'create', 'delete', 'edit', 'edit-state', 'edit-own', 'view'
		);

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
			$course = Course::getInstance(Request::getString('gid', ''));
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
			$course = Course::getInstance($this->get('course_id'));
		}

		// Make sure the course exists
		if ($course->exists() && $course->isPublished())
		{
			$this->config()->set('access-view-course', true);
		}

		// If they're logged in
		if (!User::isGuest())
		{
			$manager = $this->manager(User::get('id'));
			// If they're NOT an admin
			if ($manager->exists())
			{
				// If no specific permissions set
				if ($manager->get('course_id') == $this->get('course_id'))
				{
					if (!$manager->get('offering_id') && !$manager->get('section_id'))
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
					}
					else
					{
						$this->config()->set('access-view-section', true);
						$this->config()->set('access-view-offering', true);
						$this->config()->set('access-view-course', true);
					}

					$this->config()->set('is-manager', true);
					$this->config()->set('access-checked-offering', true);
					$this->config()->set('access-checked-section', true);
				}
			}
			// If they're a student
			else if (($student = $this->student(User::get('id'))))
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
			$oid = Request::getString('offering', '');

			//$course = \Components\Courses\Models\Course::getInstance($this->get('course_id'));
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
			$course = Course::getInstance($this->get('course_id'));
			$offering = $course->offering($this->get('offering_id'));
		}

		// Offering isn't available
		if (!$offering->exists() || !$offering->isPublished())
		{
			return;
		}

		// If they're logged in
		if (!User::isGuest())
		{
			$manager = $this->manager(User::get('id'));
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
			else if (($student = $this->student(User::get('id'))))
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
	 * @param   string  $action Action to check
	 * @param   string  $item   Item type to check action against
	 * @return  boolean True if authorized, false if not
	 */
	public function access($action='view', $item='course')
	{
		if (!$this->config()->get('access-checked-' . $item))
		{
			$this->_calculate();
		}

		if (User::authorise('core.admin', 'com_courses'))
		{
			return true;
		}

		return $this->config()->get('access-' . strtolower($action) . '-' . $item);
	}
}
