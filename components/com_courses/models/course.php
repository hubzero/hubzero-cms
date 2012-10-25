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

		//$this->course = JTable::getInstance('course', 'CoursesTable');
		$this->course = new CoursesTableCourse($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->course->load($oid);
		}
		else if (is_object($oid))
		{
			$this->course->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->course->bind($oid);
		}

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		//$this->params = JComponentHelper::getParams('com_courses');
		//$this->params->merge(new $paramsClass($this->course->params));

		$this->params = new $paramsClass($this->course->get('params'));
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
	 * Check if a property is set
	 * 
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	public function __isset($property)
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
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) 
		{
			return $this->_data[$property];
		}
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
			if (!array_key_exists($property, get_object_vars($this->course)))
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
						$this->course->set($key, $data);
					}

					$query = "(select uidNumber, 'invitees' AS role from #__courses_invitees where gidNumber=" . $this->_db->Quote($this->course->get('id')) . ")
						UNION
							(select uidNumber, 'applicants' AS role from #__courses_applicants where gidNumber=" . $this->_db->Quote($this->course->get('id')) . ")
						UNION
							(select uidNumber, 'members' AS role from #__courses_members where gidNumber=" . $this->_db->Quote($this->course->get('id')) . ")
						UNION
							(select uidNumber, 'managers' AS role from #__courses_managers where gidNumber=" . $this->_db->Quote($this->course->get('id')) . ")";

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
							$this->course->set($key, $data);
						}
					}
				}
			}
		}
		if (isset($this->course->$property)) 
		{
			return $this->course->$property;
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
		$previous = isset($this->course->$property) ? $this->course->$property : null;
		$this->course->$property = $value;
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
		if ($this->course->get('id') && $this->course->get('id') > 0) 
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
			$this->_creator = JUser::getInstance($this->course->created_by);
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
		if (!isset($this->offering) || ($id !== null && $this->offering->get('id') != $id && $this->offering->get('alias') != $id))
		{
			$this->offering = null;
			foreach ($this->offerings() as $key => $offering)
			{
				if ($offering->get('id') == $id || $offering->get('alias') == $id)
				{
					if (!is_a($offering, 'CoursesModelOffering'))
					{
						$offering = new CoursesModelOffering($offering);
						// Set the offering to the model
						$this->offerings($key, $offering);
					}
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
	public function offerings($idx=null, $model=null)
	{
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->offerings))
		{
			$this->offerings = array();

			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'instance.php');

			//$inst = JTable::getInstance('instance', 'CoursesTable');
			$tbl = new CoursesTableInstance($this->_db);
			if (($results = $tbl->getCourseInstances(array('course_id' => $this->course->get('id')))))
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');

				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelOffering($result);
				}
				$this->offerings = $results;
			}
		}

		if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->offerings[$idx]))
				{
					if ($model && is_a($model, 'CoursesModelOffering'))
					{
						$this->offerings[$idx] = $model;
					}
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
		}

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

