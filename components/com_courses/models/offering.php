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

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'instance.php');

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
	public $offering = NULL;

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
	static $_list_keys = array('members', 'managers', 'applicants', 'invitees');

	/**
	 * Constructor
	 * 
	 * @param      integer $id Course offering ID or alias
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->offering = new CoursesTableInstance($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
			$this->offering->load($oid);
		}
		else if (is_object($oid))
		{
			$this->offering->bind($oid);
		}
		else if (is_array($oid))
		{
			$this->offering->bind($oid);
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
	 * Check if a property is set
	 * 
	 * @param      string $property Name of property to set
	 * @return     boolean True if set
	 */
	/*public function __isset($property)
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
	/*public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	/*public function __get($property)
	{
		if (isset($this->offering->$property)) 
		{
			return $this->offering->$property;
		}
		else if (isset($this->_data[$property])) 
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
			if (!array_key_exists($property, get_object_vars($this->offering)))
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
						$this->offering->set($key, $data);
					}

					$query = "(select uidNumber, 'invitees' AS role from #__courses_invitees where gidNumber=" . $this->_db->Quote($this->offering->get('id')) . ")
						UNION
							(select uidNumber, 'applicants' AS role from #__courses_applicants where gidNumber=" . $this->_db->Quote($this->offering->get('id')) . ")
						UNION
							(select uidNumber, 'members' AS role from #__courses_members where gidNumber=" . $this->_db->Quote($this->offering->get('id')) . ")
						UNION
							(select uidNumber, 'managers' AS role from #__courses_managers where gidNumber=" . $this->_db->Quote($this->offering->get('id')) . ")";

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
							$this->offering->set($key, $data);
						}
					}
				}
			}
		}
		if (isset($this->offering->$property)) 
		{
			return $this->offering->$property;
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
		$previous = isset($this->offering->$property) ? $this->offering->$property : null;
		$this->offering->$property = $value;
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
			$this->_creator = JUser::getInstance($this->offering->created_by);
		}
		if ($property && is_a($this->_creator, 'JUser'))
		{
			return $this->_creator->get($property);
		}
		return $this->_creator;
	}

	/**
	 * Get the instructor of this entry
	 * 
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 * 
	 * @param      string $property JUser property to return
	 * @return     mixed
	 */
	/*public function instructor($property=null)
	{
		if (!isset($this->_instructor) || !is_object($this->_instructor))
		{
			$this->_instructor = JUser::getInstance($this->offering->instructor_id);
		}
		if ($property && is_a($this->_instructor, 'JUser'))
		{
			return $this->_instructor->get($property);
		}
		return $this->_instructor;
	}*/

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
	public function manager()
	{
		return $this->access('manage'); //in_array(JFactory::getUser()->get('id'), $this->get('managers'));
	}

	/**
	 * Check if the current user is enrolled
	 * 
	 * @return     boolean
	 */
	public function enrolled($id=0)
	{
		if (!$id)
		{
			$id = JFactory::getUser()->get('id');
		}
		return in_array($id, $this->get('members'));
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

			$filters['course_instance_id'] = (int) $this->get('id');

			if (($results = $tbl->getCourseUnits($filters)))
			{
				require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');

				foreach ($results as $key => $result)
				{
					$results[$key] = new CoursesModelUnit($result);
				}
				//$this->units = new CoursesModelIterator($results);
				/*
				$this->units = new CoursesModelIterator();
				$this->units->add(new CoursesModelUnit($result, $this->units));
				*/
			}
			else
			{
				$results = array();
			}

			$this->units = new CoursesModelIterator($results);
		}

		/*if ($idx !== null)
		{
			if (is_numeric($idx))
			{
				if (isset($this->units[$idx]))
				{
					return $this->units[$idx];
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
						foreach ($this->units as $unit)
						{
							$ids[] = (int) $unit->id;
						}
						return $ids;
					break;

					case 'alias':
						$aliases = array();
						foreach ($this->units as $unit)
						{
							$aliases[] = stripslashes($unit->alias);
						}
						return $aliases;
					break;

					case 'title':
						$title = array();
						foreach ($this->units as $unit)
						{
							$title[] = stripslashes($unit->title);
						}
						return $title;
					break;

					default:
						return $this->units;
					break;
				}
			}
		}*/

		return $this->units;
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
		if (!$this->exists()) 
		{
			return array();
		}

		if (!isset($this->pages) || !is_array($this->pages))
		{
			$this->pages = array();

			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'pages.php');

			$tbl = new CoursesTablePage($this->_db);
			if (($results = $tbl->getPages($this->get('course_id'), true)))
			{
				$this->pages = $results;
			}
			/*else
			{
				$results = array();
			}

			$this->pages = $results;*/
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
	public function access($action='view')
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
					}
					// Check if they're the offering creator or offering manager
					else if ($this->get('created_by') == $juser->get('id') 
						  || in_array($juser->get('id'), $this->get('managers'))) 
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
					else if (in_array($juser->get('id'), $this->get('members')))
					{
						$this->params->set('access-view-offering', true);
					}
				}

				$this->_authorized = true;
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-offering');
	}
}

