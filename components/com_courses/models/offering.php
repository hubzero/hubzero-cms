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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'member.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'announcement.php');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'page.php');
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
	//private $_tbl = NULL;

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
	 * CoursesModelMember
	 * 
	 * @var object
	 */
	private $_member = NULL;

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
	 * JUser
	 * 
	 * @var object
	 */
	//private $_creator = NULL;

	/**
	 * JDatabase
	 * 
	 * @var object
	 */
	//private $_db = NULL;

	/**
	 * JParameter
	 * 
	 * @var object
	 */
	public $params = NULL;

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
	public function __construct($oid, $course_id=null)
	{
		$section = '__default';

		$this->_db = JFactory::getDBO();

		$this->_tbl = new CoursesTableOffering($this->_db);

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
		else if (is_object($oid))
		{
			$this->_tbl->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->_tbl->bind($oid);
		}

		if ($this->exists() && $section)
		{
			$this->section($section);
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
	 * Method to get/set the current unit
	 *
	 * @param     mixed $id ID or alias of specific unit
	 * @return    object CoursesModelUnit
	 */
	public function section($id=null)
	{
		if (!isset($this->_section) 
		 || ($id !== null && (int) $this->_section->get('id') != $id && (string) $this->_section->get('alias') != $id))
		{
			$this->_section = null;

			if (isset($this->_section))
			{
				foreach ($this->sections() as $section)
				{
					if ((int) $section->get('id') == $id || (string) $section->get('alias') == $id)
					{
						$this->_section = $section;
						break;
					}
				}
			}
			else
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
	public function sections($filters=array())
	{
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('id');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTableSection($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_sections) || !is_a($this->_sections, 'CoursesModelIterator'))
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

		if (!isset($this->units) || !is_a($this->units, 'CoursesModelIterator'))
		{
			$tbl = new CoursesTableUnit($this->_db);

			if (($results = $tbl->find($filters)))
			{
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
			$tbl = new CoursesTableMember($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_members) || !is_array($this->_members) || $clear)
		{
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

			if (isset($this->_pages) && is_array($this->_pages) && isset($this->_pages[$url]))
			{
				$this->_page = $this->_pages[$url];
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
	public function pages($filters=array())
	{
		if (!isset($filters['offering_id']))
		{
			$filters['offering_id'] = (int) $this->get('id');
		}

		if (isset($filters['count']) && $filters['count'])
		{
			$tbl = new CoursesTablePage($this->_db);

			return $tbl->count($filters);
		}

		if (!isset($this->_pages) || !is_array($this->_pages))
		{
			$tbl = new CoursesTablePage($this->_db);

			if (!($results = $tbl->find($filters)))
			{
				$results = array();
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

			$this->_members[$user_id] = new CoursesModelMember($result, $this->get('id'));
			$this->_members[$user_id]->set('role_id', $role_id);
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
	 * Short title for 'update'
	 * Long title (if any) ...
	 *
	 * @param unknown $course_id Parameter title (if any) ...
	 * @param array $data Parameter title (if any) ...
	 * @return boolean Return title (if any) ...
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
			$section = new CoursesModelSection('__default', $this->get('id'));
			$section->set('offering_id', $this->get('id'));
			$section->set('alias', '__default');
			$section->set('title', JText::_('Default'));
			$section->set('state', 1);
			$section->set('start_date', $this->get('start_date'));
			$section->set('end_date', $this->get('end_date'));
			$section->set('publish_up', $this->get('publish_up'));
			$section->set('publish_down', $this->get('publish_down'));
			if (!$section->store())
			{
				$this->setError($section->getError());
				return false;
			}
		}

		//$affected = 0;

		JPluginHelper::importPlugin('courses');

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onAfterSaveOffering', array($this, $isNew));

		if ($isNew)
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'log.php');
			$juser = JFactory::getUser();

			$log = new CoursesTableLog($this->_db);
			$log->scope_id  = $this->get('id');
			$log->scope     = 'course';
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
	 * Delete an entry and associated data
	 * 
	 * @return     boolean True on success, false on error
	 */
	public function delete()
	{
		// Get some data for the log
		/*$log = json_encode($this->_tbl);
		$scope_id = $this->get('id');

		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		JPluginHelper::importPlugin('courses');

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onAfterDeleteOffering', array($this));

		// Log the event
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'log.php');

		$juser = JFactory::getUser();

		$log = new CoursesTableLog($this->_db);
		$log->scope_id  = $scope_id;
		$log->scope     = 'offering';
		$log->user_id   = $juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action    = 'deleted';
		$log->comments  = $log;
		$log->actor_id  = $juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}

		return true;*/
		$value = parent::delete();
		
		JPluginHelper::importPlugin('courses');

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onAfterDeleteOffering', array($this));
		
		return $value;
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

