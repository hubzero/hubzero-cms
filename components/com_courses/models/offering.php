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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'section.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'student.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'manager.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'announcement.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'page.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradebook.php');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'role.php');

/**
 * Courses model class for a course
 */
class CoursesModelOffering extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTableOffering';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'offering';

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_units = NULL;

	/**
	 * CoursesModelUnit
	 *
	 * @var object
	 */
	private $_unit = NULL;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_page = NULL;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_pages = NULL;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_announcements = NULL;

	/**
	 * JUser
	 *
	 * @var object
	 */
	private $_roles = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_members = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_member = NULL;

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
	private $_managers = NULL;

	/**
	 * CoursesModelMember
	 *
	 * @var object
	 */
	private $_student = NULL;

	/**
	 * CoursesModelGradebook
	 *
	 * @var object
	 */
	private $_gradebook = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 */
	private $_sections = NULL;

	/**
	 * CoursesModelSection
	 *
	 * @var object
	 */
	private $_section = NULL;

	/**
	 * CoursesModelIterator
	 *
	 * @var object
	 **/
	private $_assets = NULL;

	/**
	 * CoursesModelPermissions
	 *
	 * @var object
	 */
	private $_permissions = NULL;

	/**
	 * URL to this object
	 *
	 * @var string
	 */
	private $_link = NULL;

	/**
	 * JRegistry
	 *
	 * @var object
	 */
	private $_params = NULL;

	/**
	 * Constructor
	 *
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($oid, $course_id=null)
	{
		$section = '!!default!!';

		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableOffering($this->_db);

		if ($oid)
		{
			if (is_numeric($oid) || is_string($oid))
			{
				if (strstr($oid, ':'))
				{
					$dot = strpos($oid, ':');
					$section = substr($oid, $dot + 1);
					$oid = substr($oid, 0, $dot);
				}
				$this->_tbl->load($oid, $course_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}

		if ($this->exists()) // && $section)
		{
			$this->section($section);
		}
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
	static function &getInstance($oid=null, $course_id=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$key = 0;

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
			$instances[$key] = new CoursesModelOffering($oid, $course_id);
		}

		return $instances[$key];
	}

	/**
	 * Get a param value
	 *
	 * @param	   string $key     Property to return
	 * @param	   mixed  $default Default value to return
	 * @return     mixed
	 */
	public function params($key='', $default=null)
	{
		if (!($this->_params instanceof JRegistry))
		{
			$this->_params = new JRegistry($this->get('params'));
		}
		if ($key)
		{
			return $this->_params->get((string) $key, $default);
		}
		return $this->_params;
	}

	/**
	 * Method to get/set the current unit
	 *
	 * @param     mixed $id ID or alias of specific unit
	 * @return    object CoursesModelUnit
	 */
	public function section($id=null)
	{
		if ($id instanceof CoursesModelSection)
		{
			$this->_link = null;
			$this->_section = $id;
			return $this->_section;
		}

		if (!isset($this->_section)
		 || ($id !== null && (int) $this->_section->get('id') != $id && (string) $this->_section->get('alias') != $id))
		{
			$this->_section = null;
			$this->_link = null; // Clear any potential existing data that may have another (prevous) section's info

			if (isset($this->_sections))
			{
				foreach ($this->sections() as $section)
				{
					if ($id == '!!default!!')
					{
						if ($section->get('is_default'))
						{
							$this->_section = $section;
							break;
						}
					}
					else if ((int) $section->get('id') == $id || (string) $section->get('alias') == $id)
					{
						$this->_section = $section;
						break;
					}
				}
			}

			if (!$this->_section)
			{
				$this->_section = CoursesModelSection::getInstance($id, $this->get('id'));
			}
		}
		return $this->_section;
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
	public function sections($filters=array(), $clear=false)
	{
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('id');
		}
		if (!isset($filters['available']) && !JFactory::getApplication()->isAdmin())
		{
			$filters['available'] = true;
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableSection($this->_db);

			return $tbl->count($filters);
		}

		if (!($this->_sections instanceof CoursesModelIterator) || $clear)
		{
			$tbl = new CoursesTableSection($this->_db);

			if (($results = $tbl->find($filters)))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelSection($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_sections = new CoursesModelIterator($results);
		}

		return $this->_sections;
	}

	/**
	 * Method to get/set the current unit
	 *
	 * @param     mixed $id ID or alias of specific unit
	 * @return    object CoursesModelUnit
	 */
	public function unit($id=null)
	{
		if (!isset($this->_unit)
		 || ($id !== null && (int) $this->_unit->get('id') != $id && (string) $this->_unit->get('alias') != $id))
		{
			$this->_unit = null;

			if (isset($this->_units))
			{
				foreach ($this->units() as $key => $unit)
				{
					if ((int) $unit->get('id') == $id || (string) $unit->get('alias') == $id)
					{
						$this->_unit = $unit;
						$units = $this->units();
						$this->_unit->siblings($units);
						break;
					}
				}
			}

			if (is_null($this->_unit))
			{
				$this->_unit = CoursesModelUnit::getInstance($id, $this->get('id'));
			}
		}
		return $this->_unit;
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
	public function units($filters=array(), $clear=false)
	{
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('id');
		}
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->section()->get('id');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableUnit($this->_db);

			return $tbl->count($filters);
		}

		if (!($this->_units instanceof CoursesModelIterator) || $clear)
		{
			$tbl = new CoursesTableUnit($this->_db);

			if (($results = $tbl->find($filters)))
			{
				foreach ($results as $key => $result)
				{
					if (!$result->section_id)
					{
						$result->section_id = (int) $this->section()->get('id');
					}
					$results[$key] = new CoursesModelUnit($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_units = new CoursesModelIterator($results);
		}

		return $this->_units;
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
			else
			{
				$this->_manager = CoursesModelManager::getInstance($user_id, $this->get('course_id'), $this->get('id'), $this->section()->get('id'));
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
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('id');
		}
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->section()->get('id');
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
		if (!$this->access('manage', 'section') && $this->access('view', 'section'))
		{
			return true;
		}
		return false;
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

			/*if (isset($this->_members) && isset($this->_members[$user_id]))
			{
				$this->_student = $this->_members[$user_id];
			}*/
			if (isset($this->_members)) // && isset($this->_members[$user_id]))
			{
				foreach ($this->_members as $member)
				{
					if ($member->get('user_id') == $user_id && $member->get('section_id') == $this->section()->get('id') && $member->get('student') )
					{
						$this->_student = $member;
						break;
					}
				}
			}
		}

		if (!$this->_student)
		{
			$this->_student = CoursesModelStudent::getInstance($user_id, $this->get('course_id'), $this->get('id'), $this->section()->get('id'));
		}

		return $this->_student;
	}

	/**
	 * Get offering gradebook
	 *
	 * @return     obj
	 */
	public function gradebook($oid=null)
	{
		if (!isset($this->_gradebook))
		{
			$course = new CoursesModelCourse($this->get('course_id'));
			$course->offering($this->get('id'));
			$course->offering()->section($this->section()->get('id'));
			$this->_gradebook = new CoursesModelGradeBook($oid, $course);
		}

		return $this->_gradebook;
	}

	/**
	 * Get a list of assets for an offering
	 * 
	 * Accepts an array of filters to apply to the list of assets
	 *
	 * @param      array $filters Filters to apply
	 * @return     object CoursesModelIterator
	 */
	public function assets($filters=array())
	{
		if (!($this->_assets instanceof CoursesModelIterator))
		{
			if (!isset($filters['asset_scope_id']))
			{
				$filters['asset_scope_id'] = (int) $this->get('id');
			}
			if (!isset($filters['asset_scope']))
			{
				$filters['asset_scope']    = 'offering';
			}

			$tbl = new CoursesTableAsset($this->_db);

			if (($results = $tbl->find(array('w' => $filters))))
			{
				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelAsset($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->_assets = new CoursesModelIterator($results);
		}

		return $this->_assets;
	}

	/**
	 * Check if the current user is enrolled
	 *
	 * @return     boolean
	 */
	public function students($filters=array(), $clear=false)
	{
		$filters['student'] = 1;

		return $this->members($filters, $clear);
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

			if (isset($this->_members)) // && isset($this->_members[$user_id]))
			{
				foreach ($this->_members as $member)
				{
					if ($member->get('user_id') == $user_id && $member->get('section_id') == $this->section()->get('id'))
					{
						$this->_member = $member;
						break;
					}
				}
			}
		}

		if (!$this->_member)
		{
			$this->_member = CoursesModelMember::getInstance($user_id, $this->get('course_id'), $this->get('id'), $this->section()->get('id'));
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
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('course_id');
		}
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = array(0, (int) $this->get('id'));
		}
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = array(0, (int) $this->section()->get('id'));
		}
		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'student ASC, section_id ASC, offering_id';
		}
		if (!isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = 'ASC';
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableMember($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_members) || !is_array($this->_members) || $clear)
		{
			$tbl = new CoursesTableMember($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				$mdl = 'CoursesModelMember';
				if (isset($filters['student']) && $filters['student'])
				{
					$mdl = 'CoursesModelStudent';
				}
				else if (isset($filters['student']) && !$filters['student'])
				{
					$mdl = 'CoursesModelManager';
				}

				foreach ($data as $key => $result)
				{
					if (!isset($results[$result->user_id]))
					{
						$results[$key] = new $mdl($result);
					}
				}
			}

			$this->_members = $results; //new CoursesModelIterator($results);
		}

		return $this->_members;
	}

	/**
	 * Get a list of memerships for a user
	 *
	 * @param      array   $filters Filters to build query from
	 * @return     mixed
	 */
	public function membership($user_id=0)
	{
		$filters = array();
		if (!isset($filters['course_id']))
		{
			$filters['course_id'] = (int) $this->get('course_id');
		}
		/*if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('id');
		}*/
		if (!$user_id)
		{
			$user_id = JFactory::getUser()->get('id');
		}
		$filters['user_id'] = (int) $user_id;
		$filters['sort'] = 'offering_id ASC, student';
		$filters['sort_Dir'] = 'ASC';

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableMember($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_membership[$user_id]) || !is_array($this->_membership[$user_id]))
		{
			$tbl = new CoursesTableMember($this->_db);

			$results = array();

			if (($results = $tbl->find($filters)))
			{
				foreach ($results as $key => $result)
				{
					$mdl = 'CoursesModelMember';
					if ($result->student)
					{
						$mdl = 'CoursesModelStudent';
					}
					else
					{
						$mdl = 'CoursesModelManager';
					}
					$results[$key] = new $mdl($result);
				}
			}

			$this->_membership[$user_id] = $results; //new CoursesModelIterator($results);
		}

		return $this->_membership[$user_id];
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
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = array(0, (int) $this->get('id'));  // 0 = default roles
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableRole($this->_db);

			return $tbl->count($filters);
		}
		if (!isset($this->_roles) || !is_array($this->_roles) || $clear)
		{
			$tbl = new CoursesTableRole($this->_db);

			if (!($results = $tbl->find($filters)))
			{
				$results = array();
			}

			$this->_roles = $results; //new CoursesModelIterator($results);
		}

		return $this->_roles;
	}

	/**
	 * Check if the current user is enrolled
	 *
	 * @return     boolean
	 */
	public function page($url=null)
	{
		if (!isset($this->_page)
		 || ($url !== null && (int) $this->_page['url'] != $url))
		{
			$this->_page = null;

			//if (isset($this->_pages) && is_array($this->_pages))
			//{
				foreach ($this->pages() as $page)
				{
					if ($page->get('url') == $url)
					{
						$this->_page = $page;
					}
				}
			//}
			if (!$this->_page)
			{
				$this->_page = new CoursesModelPage(0);
			}
		}

		return $this->_page;
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
	public function pages($filters=array(), $reload=false)
	{
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('id');
		}
		if (!isset($filters['active']))
		{
			$filters['active'] = 1;
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

		if (!isset($this->_pages) || !is_array($this->_pages) || $reload)
		{
			$tbl = new CoursesTablePage($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					$results[] = new CoursesModelPage($result);
				}
			}

			$this->_pages = $results;
		}

		return $this->_pages;
	}

	/**
	 * Get a list of announcements for an offering
	 *   Accepts an array of filters to retrieve data by
	 *
	 * @param      array $filters
	 * @return     mixed
	 */
	public function announcements($filters=array())
	{
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('id');
		}
		if (!isset($filters['section_id']))
		{
			$filters['section_id'] = (int) $this->section()->get('id');
		}
		if (!isset($filters['state']))
		{
			$filters['state'] = 1;
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableAnnouncement($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_announcements) || !is_array($this->_announcements))
		{
			$tbl = new CoursesTableAnnouncement($this->_db);

			$results = array();

			if (($data = $tbl->find($filters)))
			{
				foreach ($data as $key => $result)
				{
					$results[] = new CoursesModelAnnouncement($result);
				}
			}

			$this->_announcements = new CoursesModelIterator($results);
		}

		return $this->_announcements;
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
			$this->importPlugin('courses');

			$plugins = $this->trigger('onCourseAreas', array());

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
		if (!isset($this->_permissions))
		{
			$this->_permissions = CoursesModelPermissions::getInstance();
			$this->_permissions->set('course_id', $this->get('course_id'));
			$this->_permissions->set('offering_id', $this->get('id'));
			$this->_permissions->set('section_id', $this->section()->get('id'));
		}
		return $this->_permissions->access($action, $item);
	}

	/**
	 * Add one or more user IDs or usernames to the managers list
	 *
	 * @param     array $value List of IDs or usernames
	 * @return    void
	 */
	public function add($data = array(), $role_id='student')
	{
		if (!is_array($data))
		{
			$data = array($data);
		}

		$role = new CoursesTableRole($this->_db);
		$role->load($role_id);
		if (is_string($role_id))
		{
			$role_id = $role->get('id');
		}

		foreach ($data as $result)
		{
			$user_id = (int) $this->_userId($result);

			$model = CoursesModelMember::getInstance($user_id, $this->get('course_id'), $this->get('id'), $this->section()->get('id'));
			$model->set('user_id', $user_id);
			$model->set('course_id', $this->get('course_id'));
			$model->set('offering_id', $this->get('id'));
			$model->set('section_id', $this->section()->get('id'));
			$model->set('role_id', $role_id);
			if ($role->get('alias') == 'student')
			{
				$model->set('student', 1);
			}
			if (!$model->store())
			{
				$this->setError($model->getError());
				continue;
			}

			$this->_managers[$user_id] = $model;
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
		if (!is_array($data))
		{
			$data = array($data);
		}
		if (count($data) > 0)
		{
			$this->members();

			foreach ($data as $result)
			{
				$user_id = $this->_userId($result);

				if (isset($this->_members[$user_id]))
				{
					$this->_members[$user_id]->delete();
					unset($this->_members[$user_id]);
				}
			}
			$this->_managers = null;
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

		$this->_db->setQuery("SELECT id FROM `#__users` WHERE username=" . $this->_db->Quote($user));

		if (($result = $this->_db->loadResult()))
		{
			return $result;
		}

		return 0;
	}

	/**
	 * Create a section linked to this offering
	 *
	 * @param   boolean $validate Validate data?
	 * @return  boolean True on success, False on error
	 */
	public function makeSection($alias='__default', $is_default=1)
	{
		$section = new CoursesModelSection();
		$section->set('offering_id', $this->get('id'));
		$section->set('alias', $alias);
		$section->set('title', JText::_('Default'));
		$section->set('state', 1);
		$section->set('is_default', $is_default);
		$section->set('enrollment', $this->config('default_enrollment', 0));
		$section->set('start_date', $this->get('start_date'));
		$section->set('end_date', $this->get('end_date'));
		$section->set('publish_up', $this->get('publish_up'));
		$section->set('publish_down', $this->get('publish_down'));
		if (!$section->store())
		{
			$this->setError($section->getError());
			return false;
		}

		$this->_sections = null;

		return true;
	}

	/**
	 * Store changes to the database
	 *
	 * @param   boolean $validate Validate data?
	 * @return  boolean True on success, False on error
	 */
	public function store($validate=true)
	{
		if (empty($this->_db))
		{
			return false;
		}

		$isNew = ($this->get('id') ? false : true);

		// Validate data?
		if ($validate)
		{
			if (!$this->_tbl->check())
			{
				$this->setError($this->_tbl->getError());
				return false;
			}
		}

		// Store data
		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		// Check for sections
		// An offering MUST have at least one __default section
		if ($validate && $this->sections()->total() <= 0)
		{
			if (!$this->makeSection('__default', 1))
			{
				return false;
			}
		}

		$this->importPlugin('courses')
		     ->trigger('onOfferingSave', array($this));

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
		// Remove pages
		foreach ($this->pages(array('active' => array(0, 1)), true) as $page)
		{
			if (!$page->delete())
			{
				$this->setError($page->getError());
			}
		}

		// Remove announcements
		foreach ($this->announcements(array('section_id' => -1, 'state' => -1)) as $announcement)
		{
			if (!$announcement->delete())
			{
				$this->setError($announcement->getError());
			}
		}

		// Remove any units
		foreach ($this->units(array('section_id' => -1), true) as $unit)
		{
			if (!$unit->delete())
			{
				$this->setError($unit->getError());
			}
		}

		// Remove sections
		// Each section will also remove any students in that section
		foreach ($this->sections() as $section)
		{
			if (!$section->delete())
			{
				$this->setError($section->getError());
			}
		}

		$value = parent::delete();

		$this->importPlugin('courses')
		     ->trigger('onOfferingDelete', array($this));

		return $value;
	}

	/**
	 * Copy an entry and associated data
	 *
	 * @param   integer $course_id New course to copy to
	 * @param   boolean $deep      Copy associated data?
	 * @return  boolean True on success, false on error
	 */
	public function copy($course_id=null, $deep=true)
	{
		// Get some old info we may need
		//  - Offering ID
		//  - Course ID
		$o_id = $this->get('id');
		$c_id = $this->get('course_id');

		// Reset the ID. This will force store() to create a new record.
		$this->set('id', 0);
		// Are we copying to a new course?
		if ($course_id)
		{
			$this->set('course_id', $course_id);
		}
		else
		{
			// Copying to the same course so we want to distinguish
			// this offering from the one we copied from
			$this->set('title', $this->get('title') . ' (copy)');
			$this->set('alias', $this->get('alias') . '_copy');
		}
		if (!$this->store())
		{
			return false;
		}

		if ($deep)
		{
			// Copy pages
			foreach ($this->pages(array('offering_id' => $o_id, 'active' => array(0, 1)), true) as $page)
			{
				if (!$page->copy($this->get('course_id'), $this->get('id')))
				{
					$this->setError($page->getError());
				}
			}

			// Copy units
			foreach ($this->units(array('offering_id' => $o_id, 'section_id' => -1), true) as $unit)
			{
				if (!$unit->copy($this->get('id')))
				{
					$this->setError($unit->getError());
				}
			}

			// Copy logo
			if ($file = $this->logo('file'))
			{
				$src  = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $c_id . '/offerings/' . $o_id . '/' . $file;
				if (file_exists(JPATH_ROOT . $src))
				{
					$dest = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $this->get('course_id') . '/offerings/' . $this->get('id');

					if (!is_dir(JPATH_ROOT . $dest))
					{
						jimport('joomla.filesystem.folder');
						if (!JFolder::create(JPATH_ROOT . $dest))
						{
							$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
						}
					}

					$dest .= '/' . $file;

					jimport('joomla.filesystem.file');
					if (!JFile::copy($src, $dest, JPATH_ROOT))
					{
						$this->setError(JText::_('Failed to copy offering logo.'));
					}
				}
			}
		}

		return true;
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
		$this->importPlugin('courses');

		// Trigger the functions that return the areas we'll be using
		//then add overview to array
		$hub_course_plugins = $this->trigger('onCourseAreas', array());
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

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
	 */
	public function link($type='')
	{
		if (!isset($this->_link))
		{
			if (!$this->get('course_alias'))
			{
				$course = CoursesModelCourse::getInstance($this->get('course_id'));
				$this->set('course_alias', $course->get('alias'));
			}
			$this->_link  = 'index.php?option=com_courses&controller=offering&gid=' . $this->get('course_alias') . '&offering=' . $this->alias();
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link = $this->_link . '&task=edit';
			break;

			case 'delete':
				$link = $this->_link . '&task=delete';
			break;

			case 'enroll':
				$this->importPlugin('courses');

				$course = CoursesModelCourse::getInstance($this->get('course_id'));

				$data = $this->trigger('onCourseEnrollLink', array(
					$course, $this, $this->section()
				));
				if ($data && count($data) > 0)
				{
					$link = implode('', $data);
				}
				else
				{
					$link = $this->_link . '&task=enroll';
				}
			break;

			case 'overview':
				$link = 'index.php?option=com_courses&gid=' . $this->get('course_alias');
			break;

			case 'permalink':
			default:
				$link = $this->_link;
			break;
		}

		return $link;
	}

	/**
	 * Get the offering alias with section alias
	 *
	 * @return     string
	 */
	public function alias()
	{
		return $this->get('alias') . ($this->section()->get('is_default') ? '' : ':' . $this->section()->get('alias'));
	}

	/**
	 * Get the offering logo
	 *
	 * @param      string $rtrn Property to return
	 * @return     string
	 */
	public function logo($rtrn='')
	{
		$rtrn = strtolower(trim($rtrn));

		// Return just the file name
		if ($rtrn == 'file')
		{
			return $this->params('logo');
		}

		// Build the path
		$path = '/' . trim($this->config('uploadpath', '/site/courses'), '/') . '/' . $this->get('course_id') . '/offerings/' . $this->get('id');

		// Return just the upload path?
		if ($rtrn == 'path')
		{
			return $path;
		}

		// Do we have a logo set?
		if ($file = $this->params('logo'))
		{
			// Return the web path to the image
			$path .= '/' . $file;
			if (file_exists(JPATH_ROOT . $path))
			{
				$path = str_replace('/administrator', '', \JURI::base(true)) . $path;
			}
			return $path;
		}

		return '';
	}
}

