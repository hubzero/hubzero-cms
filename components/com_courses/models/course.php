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
	static $_list_keys = array('members', 'managers', 'applicants', 'invitees');

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
				if (is_object($this->_db))
				{
					$membership = array(
						'applicants' => array(), 
						'invitees'   => array(), 
						'members'    => array(), 
						'managers'   => array()
					);

					foreach ($membership as $key => $data)
					{
						$this->_tbl->$key = $data;
					}

					$query = "(select uidNumber, 'invitees' AS role from #__courses_invitees where gidNumber=" . $this->_db->Quote($this->_tbl->get('id')) . ")
						UNION
							(select uidNumber, 'applicants' AS role from #__courses_applicants where gidNumber=" . $this->_db->Quote($this->_tbl->get('id')) . ")
						UNION
							(select uidNumber, 'members' AS role from #__courses_members where gidNumber=" . $this->_db->Quote($this->_tbl->get('id')) . ")
						UNION
							(select uidNumber, 'managers' AS role from #__courses_managers where gidNumber=" . $this->_db->Quote($this->_tbl->get('id')) . ")";

					$this->_db->setQuery($query);

					if (($results = $this->_db->loadObjectList()))
					{
						foreach ($results as $result)
						{
							if (isset($membership[$result->role]))
							{
								$membership[$result->role][] = $result->uidNumber;
							}
						}

						foreach ($membership as $key => $data)
						{
							$this->_tbl->$key = $data;
						}
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
		if (!isset($this->offering) 
		 || ($id !== null && (int) $this->offering->get('id') != $id && (string) $this->offering->get('alias') != $id))
		{
			$this->offering = null;
			foreach ($this->offerings() as $key => $offering)
			{
				if ((int) $offering->get('id') == $id || (string) $offering->get('alias') == $id)
				{
					$this->offering = $offering;
					break;
				}
			}
		}
		return $this->offering;
	}

	/**
	 * Get a list of offerings for a course
	 *   Accepts either a numeric array index or a string [id, name]
	 *   If index, it'll return the entry matching that index in the list
	 *   If string, it'll return either a list of IDs or names
	 * 
	 * @param      mixed $idx Index value
	 * @return     array
	 */
	public function offerings($filters=array())
	{
		if (!isset($this->offerings) || !is_a($this->offerings, 'CoursesModelIterator'))
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'iterator.php');
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'instance.php');

			$tbl = new CoursesTableInstance($this->_db);

			$filters['course_id'] = (int) $this->get('id');

			if (($results = $tbl->getCourseInstances($filters)))
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');

				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelOffering($result);
				}
			}
			else
			{
				$results = array();
			}

			$this->offerings = new CoursesModelIterator($results);
		}

		/*if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->offerings[$idx]))
				{
					return $this->offerings[$idx];
				}
				else
				{
					$this->setError(JText::_('Index not found: ') . __CLASS__ . '::' . __METHOD__ . '[' . $idx . ']');
					return false;
				}
			}
			else if (is_string($idx))
			{
				$idx = strtolower(trim($idx));
				switch ($idx)
				{
					case 'id':
						$ids = array();
						foreach ($this->offerings as $offering)
						{
							$ids[] = (int) $offering->id;
						}
						return $ids;
					break;

					case 'alias':
						$aliases = array();
						foreach ($this->offerings as $offering)
						{
							$aliases[] = stripslashes($offering->alias);
						}
						return $aliases;
					break;

					case 'title':
						$title = array();
						foreach ($this->offerings as $offering)
						{
							$title[] = stripslashes($offering->title);
						}
						return $title;
					break;

					default:
						return $this->offerings;
					break;
				}
			}
		}*/

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

			if ($this->exists() && $this->get('state') == 1)
			{
				$this->params->set('access-view-course', true);
			}

			$juser = JFactory::getUser();
			if ($juser->get('guest'))
			{
				$this->_authorized = true;
			}
			else
			{
				// Anyone logged in can create a course
				$this->params->set('access-create-course', true);

				// Check if they're a site admin
				if (version_compare(JVERSION, '1.6', 'lt'))
				{
					if ($juser->authorize('com_courses', 'manage')) 
					{
						$this->params->set('access-admin-course', true);
						$this->params->set('access-manage-course', true);
						$this->params->set('access-delete-course', true);
						$this->params->set('access-edit-course', true);
						$this->params->set('access-edit-state-course', true);
						$this->params->set('access-edit-own-course', true);
					}
				}
				else 
				{
					$this->params->set('access-admin-course', $juser->authorise('core.admin', $this->get('id')));
					$this->params->set('access-manage-course', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-delete-course', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-course', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-state-course', $juser->authorise('core.manage', $this->get('id')));
					$this->params->set('access-edit-own-course', $juser->authorise('core.manage', $this->get('id')));
				}

				// If they're not an admin
				if (!$this->params->get('access-admin-course') 
				 && !$this->params->get('access-manage-course'))
				{
					// Does the course exist?
					if (!$this->exists())
					{
						// Give editing access if the course doesn't exist
						// i.e., it's a new course
						$this->params->set('access-view-course', true);
						$this->params->set('access-delete-course', true);
						$this->params->set('access-edit-course', true);
						$this->params->set('access-edit-state-course', true);
						$this->params->set('access-edit-own-course', true);
					}
					// Check if they're the course creator or course manager
					else if ($this->get('created_by') == $juser->get('id') 
						  || in_array($juser->get('id'), $this->get('managers'))) 
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
}

