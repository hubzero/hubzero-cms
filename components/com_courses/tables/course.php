<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.helper');
jimport('joomla.plugin.helper');
ximport('Hubzero_Validate');

/**
 * Courses table class for a course
*/
class CoursesCourse
{
	
	/**
	 * Description for 'gidNumber'
	 *
	 * @var string
	 */
	private $gidNumber = null;
	
	/**
	 * Description for 'cn'
	 *
	 * @var unknown
	 */
	private $cn = null;
	
	/**
	 * Description for 'group_id'
	 *
	 * @var string
	 */
	private $group_id = null;
	
	/**
	 * Description for 'description'
	 *
	 * @var unknown
	 */
	private $description = null;
	
	/**
	 * Description for 'published'
	 *
	 * @var unknown
	 */
	private $published = null;
	
	/**
	 * Description for 'type'
	 *
	 * @var unknown
	 */
	private $type = null;
	
	/**
	 * Description for 'access'
	 *
	 * @var unknown
	 */
	private $access = null;
	
	/**
	 * Description for 'public_desc'
	 *
	 * @var unknown
	 */
	private $public_desc = null;
	
	/**
	 * Description for 'private_desc'
	 *
	 * @var unknown
	 */
	private $private_desc = null;
	
	/**
	 * Description for 'restrict_msg'
	 *
	 * @var unknown
	 */
	private $restrict_msg = null;
	
	/**
	 * Description for 'join_policy'
	 *
	 * @var unknown
	 */
	private $join_policy = null;
	
	/**
	 * Description for 'privacy'
	 *
	 * @var unknown
	 */
	private $privacy = null;
	
	/**
	 * Description for 'discussion_email_autosubscribe'
	 *
	 * @var tinyint
	 */
	private $discussion_email_autosubscribe = 0;
	
	/**
	 * Description for 'logo'
	 *
	 * @var unknown
	 */
	private $logo = null;
	
	/**
	 * Description for 'overview_type'
	 *
	 * @var unknown
	 */
	private $overview_type = null;
	
	/**
	 * Description for 'overview_content'
	 *
	 * @var unknown
	 */
	private $overview_content = null;
	
	/**
	 * Description for 'plugins'
	 *
	 * @var unknown
	 */
	private $plugins = null;
	
	/**
	 * Description for 'created'
	 *
	 * @var unknown
	 */
	private $created = null;
	
	/**
	 * Description for 'created_by'
	 *
	 * @var unknown
	 */
	private $created_by = null;
	
	/**
	 * Description for 'params'
	 *
	 * @var unknown
	 */
	private $params = null;
	
	/**
	 * Description for 'members'
	 *
	 * @var array
	 */
	private $members = array();
	
	/**
	 * Description for 'managers'
	 *
	 * @var array
	 */
	private $managers = array();
	
	/**
	 * Description for 'applicants'
	 *
	 * @var array
	 */
	private $applicants = array();
	
	/**
	 * Description for 'invitees'
	 *
	 * @var array
	 */
	private $invitees = array();
	
	/**
	 * Description for '_list_keys'
	 *
	 * @var array
	 */
	static $_list_keys = array('members', 'managers', 'applicants', 'invitees');
	
	/**
	 * Description for '_updatedkeys'
	 *
	 * @var array
	 */
	private $_updatedkeys = array();

	/**
	 * Short description for '__construct'
	 * Long description (if any) ...
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Short description for 'clear'
	 * Long description (if any) ...
	 *
	 * @return boolean Return description (if any) ...
	 */
	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);

		$this->_updatedkeys = array();

		foreach ($cvars as $key=>$value)
		{
			if ($key{0} != '_')
			{
				unset($this->$key);

				if (!in_array($key, self::$_list_keys))
				{
					$this->$key = null;
				}
				else
				{
					$this->$key = array();
				}
			}
		}

		$this->_updatedkeys = array();

		return true;
	}

	/**
	 * Short description for 'getInstance'
	 * Long description (if any) ...
	 *
	 * @param unknown $course Parameter description (if any) ...
	 * @return mixed Return description (if any) ...
	 */
	static public function getInstance($course)
	{
		$hzg = new CoursesCourse();

		if ($hzg->read($course) === false)
		{
			return false;
		}

		return $hzg;
	}

	/**
	 * Short description for 'create'
	 * Long description (if any) ...
	 *
	 * @param unknown $cn Parameter description (if any) ...
	 * @param unknown $gidNumber Parameter description (if any) ...
	 * @return mixed Return description (if any) ...
	 */
	public function create()
	{
		$db = &JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		$cn = $this->cn;
		$gidNumber = $this->gidNumber;

		if (empty($cn) && empty($gidNumber))
		{
			return false;
		}

		if (is_numeric($gidNumber))
		{
			if (empty($cn))
			{
				$cn = '_gid' . $gidNumber;
			}

			$query = "INSERT INTO #__courses (gidNumber,cn) VALUES (" . $db->Quote($gidNumber) . "," . $db->Quote($cn) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() != 1062)
			{
				return false;
			}
		}
		else
		{
			$query = "INSERT INTO #__courses (cn) VALUES (" . $db->Quote($cn) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() == 1062) // row exists
			{
				$query = "SELECT gidNumber FROM #__courses WHERE cn=" . $db->Quote($cn) . ";";

				$db->setQeury($query);

				$result = $db->loadResult();

				if (empty($result))
				{
					return false;
				}

				$this->__set('gidNumber', $result);
			}
			else if ($result !== false)
			{
				$this->__set('gidNumber', $db->insertid());
			}
			else
			{
				return false;
			}
		}

		JPluginHelper::importPlugin('user');

		//trigger the onAfterStoreCourse event
		$dispatcher = & JDispatcher::getInstance();
		$dispatcher->trigger('onAfterStoreCourse', array($this));

		return $this->gidNumber;
	}

	/**
	 * Short description for 'read'
	 * Long description (if any) ...
	 *
	 * @param unknown $name Parameter description (if any) ...
	 * @param string $storage Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function read($name = null)
	{
		$this->clear();

		$db = JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (!is_null($name))
		{
			if (Hubzero_Validate::is_positive_integer($name))
			{
				$this->gidNumber = $name;
			}
			else
			{
				$this->cn = $name;
			}
		}

		$result = true;
		$lazyloading = false;

		if (is_numeric($this->gidNumber))
		{
			$query = "SELECT * FROM #__courses WHERE gidNumber = " . $db->Quote($this->gidNumber) . ";";
		}
		else
		{
			$query = "SELECT * FROM #__courses WHERE cn = " . $db->Quote($this->cn) . ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result))
		{
			$this->clear();
			return false;
		}

		foreach ($result as $key=>$value)
		{
			if (property_exists(__CLASS__, $key) && $key{0} != '_')
			{
				$this->__set($key, $value);
			}
		}

		$this->__unset('members'); // we unset the lists so we can detect whether they have been loaded or not
		$this->__unset('invitees');
		$this->__unset('applicants');
		$this->__unset('managers');

		if (!$lazyloading)
		{
			$this->__get('members');
			$this->__get('invitees');
			$this->__get('applicants');
			$this->__get('managers');
		}

		$this->_updatedkeys = array();

		return true;
	}

	/**
	 * Short description for 'update'
	 * Long description (if any) ...
	 *
	 * @param unknown $gidNumber Parameter description (if any) ...
	 * @param array $data Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function update()
	{
		$db = JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (!is_numeric($this->gidNumber))
		{
			return false;
		}

		if (empty($this->_updatedkeys))
		{
			return true;
		}

		$query = "UPDATE #__courses SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;
		$affected = 0;

		foreach ($classvars as $property=>$value)
		{
			if (($property{0} == '_') || in_array($property, self::$_list_keys))
			{
				continue;
			}

			if (!in_array($property, $this->_updatedkeys))
			{
				continue;
			}

			if (!$first)
			{
				$query .= ',';
			}
			else
			{
				$first = false;
			}

			$value = $this->$property;

			if ($value === null)
			{
				$query .= "`$property`=NULL";
			}
			else
			{
				$query .= "`$property`=" . $db->Quote($value);
			}
		}

		$query .= " WHERE `gidNumber`=" . $db->Quote($this->gidNumber) . ";";

		if (($first != true) && !empty($query))
		{
			$db->setQuery($query);

			$result = $db->query();

			if ($result === false)
			{
				return false;
			}

			$affected = $db->getAffectedRows();
		}

		$aNewUserCourseEnrollments = array();

		foreach (self::$_list_keys as $property)
		{
			if (!in_array($property, $this->_updatedkeys))
			{
				continue;
			}

			$aux_table = "#__courses_" . $property;

			$list = $this->$property;

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

				$ulist .= $db->Quote($value);
				$tlist .= '(' . $db->Quote($this->gidNumber) . ',' . $db->Quote($value) . ')';
			}

			// @FIXME: I don't have a better solution yet. But the next refactoring of this class
			// should eliminate the ability to read the entire member table due to problems with
			// scale on a large (thousands of members) courses. The add function should track the members
			// being added to a course, but would need to be verified to handle adding members
			// already in course. *njk*

			// @FIXME: Not neat, but because all course membership is resaved every time even for single additions
			// there is no nice way to detect only *new* additions without this check. I don't want to 
			// fire off an 'onUserCourseEnrollment' event for users unless they are really being enrolled. *drb*

			if (in_array($property, array('members', 'managers')))
			{
				$query = "SELECT uidNumber FROM #__courses_members WHERE gidNumber=" . $this->gidNumber;
				$db->setQuery($query);

				// compile current list of members in this course
				$aExistingUserMembership = array();

				if (($results = $db->loadAssoc()))
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
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees')))
				{
					$query = "REPLACE INTO $aux_table (gidNumber,uidNumber) VALUES $tlist;";
				}

				$db->setQuery($query);

				if ($db->query())
				{
					$affected += $db->getAffectedRows();
				}
			}

			if (!is_array($list) || count($list) == 0)
			{
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees')))
				{
					$query = "DELETE FROM $aux_table WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";";
				}
			}
			else
			{
				if (in_array($property, array('members', 'managers', 'applicants', 'invitees')))
				{
					$query = "DELETE m FROM #__courses_$property AS m WHERE " . " m.gidNumber=" . 
						$db->Quote($this->gidNumber) . " AND m.uidNumber NOT IN (" . $ulist . ");";
				}
			}

			$db->setQuery($query);

			if ($db->query())
			{
				$affected += $db->getAffectedRows();
			}
		}

		// After SQL is done and has no errors, fire off onCourseUserEnrolledEvents 
		// for every user added to this course
		JPluginHelper::importPlugin('courses');
		$dispatcher = & JDispatcher::getInstance();

		foreach ($aNewUserCourseEnrollments as $userid)
		{
			$dispatcher->trigger('onCourseUserEnrollment', array($this->gidNumber, $userid));
		}

		if ($affected > 0)
		{
			JPluginHelper::importPlugin('user');
			
			//trigger the onAfterStoreCourse event
			$dispatcher = & JDispatcher::getInstance();
			$dispatcher->trigger('onAfterStoreCourse', array($this));
		}

		return true;
	}

	/**
	 * Short description for 'delete'
	 * Long description (if any) ...
	 *
	 * @param unknown $course Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function delete()
	{
		$db = JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (!is_numeric($this->gidNumber))
		{
			$db->setQuery("SELECT gidNumber FROM #__courses WHERE cn=" . $db->Quote($this->cn) . ";");

			$gidNumber = $db->loadResult();

			if (!is_numeric($this->gidNumber))
			{
				return false;
			}

			$this->gidNumber = $gidNumber;
		}

		$db->setQuery("DELETE FROM #__courses WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");

		$result = $db->query();

		if ($result === false)
		{
			return false;
		}

		$db->setQuery("DELETE FROM #__courses_applicants WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__courses_invitees WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__courses_managers WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");
		$db->query();
		$db->setQuery("DELETE FROM #__courses_members WHERE gidNumber=" . $db->Quote($this->gidNumber) . ";");
		$db->query();

		JPluginHelper::importPlugin('user');

		//trigger the onAfterStoreCourse event
		$dispatcher = & JDispatcher::getInstance();
		$dispatcher->trigger('onAfterStoreCourse', array($this));

		return true;
	}

	/**
	 * Short description for '__get'
	 * Long description (if any) ...
	 *
	 * @param string $property Parameter description (if any) ...
	 * @return string Return description (if any) ...
	 */
	public function __get($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		if (in_array($property, self::$_list_keys))
		{
			if (!array_key_exists($property, get_object_vars($this)))
			{
				$db = &JFactory::getDBO();

				if (is_object($db))
				{
					$courses = array('applicants'=>array(), 'invitees'=>array(), 'members'=>array(), 'managers'=>array());

					foreach ($courses as $key=>$data)
					{
						$this->__set($key, $data);
					}

					$query = "(select uidNumber, 'invitees' AS role from #__courses_invitees where gidNumber=" . $db->Quote($this->gidNumber) . ")
						UNION
							(select uidNumber, 'applicants' AS role from #__courses_applicants where gidNumber=" . $db->Quote($this->gidNumber) . ")
						UNION
							(select uidNumber, 'members' AS role from #__courses_members where gidNumber=" . $db->Quote($this->gidNumber) . ")
						UNION
							(select uidNumber, 'managers' AS role from #__courses_managers where gidNumber=" . $db->Quote($this->gidNumber) . ")";
					
					$db->setQuery($query);

					if (($results = $db->loadObjectList()))
					{
						foreach ($results as $result)
						{
							if (isset($courses[$result->role]))
							{
								$courses[$result->role][] = $result->uidNumber;
							}
						}

						foreach ($courses as $key=>$data)
						{
							$this->__set($key, $data);
						}
					}
				}
			}
		}

		if (isset($this->$property))
		{
			return $this->$property;
		}

		if (array_key_exists($property, get_object_vars($this)))
		{
			return null;
		}

		$this->_error("Undefined property " . __CLASS__ . "::$" . $property, E_USER_NOTICE);

		return null;
	}

	/**
	 * Short description for '__set'
	 * Long description (if any) ...
	 *
	 * @param string $property Parameter description (if any) ...
	 * @param unknown $value Parameter description (if any) ...
	 * @return void
	 */
	public function __set($property = null, $value = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		if (in_array($property, self::$_list_keys))
		{
			$value = array_diff((array) $value, array(''));

			if (in_array($property, array('managers', 'members', 'applicants', 'invitees')))
			{
				$value = $this->_userids($value);
			}

			$value = array_unique($value);
			$value = array_values($value);
			$this->$property = $value;
		}
		else
		{
			$this->$property = $value;
		}

		if (!in_array($property, $this->_updatedkeys))
		{
			$this->_updatedkeys[] = $property;
		}
	}

	/**
	 * Short description for '__isset'
	 * Long description (if any) ...
	 *
	 * @param string $property Parameter description (if any) ...
	 * @return string Return description (if any) ...
	 */
	public function __isset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		return isset($this->$property);
	}

	/**
	 * Short description for '__unset'
	 * Long description (if any) ...
	 *
	 * @param string $property Parameter description (if any) ...
	 * @return void
	 */
	public function __unset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		$this->_updatedkeys = array_diff($this->_updatedkeys, array($property));

		unset($this->$property);
	}

	/**
	 * Short description for '_error'
	 * Long description (if any) ...
	 *
	 * @param string $message Parameter description (if any) ...
	 * @param integer $level Parameter description (if any) ...
	 * @return void
	 */
	private function _error($message, $level = E_USER_NOTICE)
	{
		$caller = next(debug_backtrace());

		switch ($level)
		{
			case E_USER_NOTICE:
				echo "Notice: ";
				break;
			case E_USER_ERROR:
				echo "Fatal error: ";
				break;
			default:
				echo "Unknown error: ";
				break;
		}

		echo $message . ' in ' . $caller['file'] . ' on line ' . $caller['line'] . "\n";
	}

	/**
	 * Short description for 'get'
	 * Long description (if any) ...
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @return unknown Return description (if any) ...
	 */
	public function get($key)
	{
		return $this->__get($key);
	}

	/**
	 * Short description for 'set'
	 * Long description (if any) ...
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @param unknown $value Parameter description (if any) ...
	 * @return unknown Return description (if any) ...
	 */
	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	/**
	 * Short description for '_userids'
	 * Long description (if any) ...
	 *
	 * @param array $users Parameter description (if any) ...
	 * @return mixed Return description (if any) ...
	 */
	private function _userids($users)
	{
		$db = JFactory::getDBO();

		if (empty($db))
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
				$usernames[] = $db->Quote($u);
			}
		}

		if (empty($usernames))
		{
			return $userids;
		}

		$set = implode($usernames, ",");

		$sql = "SELECT id FROM #__users WHERE username IN ($set);";

		$db->setQuery($sql);

		$result = $db->loadResultArray();

		if (empty($result))
		{
			$result = array();
		}

		$result = array_merge($result, $userids);

		return $result;
	}

	/**
	 * Short description for 'add'
	 * Long description (if any) ...
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @param array $value Parameter description (if any) ...
	 * @return void
	 */
	public function add($key = null, $value = array())
	{
		$users = $this->_userids($value);

		$this->__set($key, array_merge($this->__get($key), $users));
	}

	/**
	 * Short description for 'remove'
	 * Long description (if any) ...
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @param array $value Parameter description (if any) ...
	 * @return void
	 */
	public function remove($key = null, $value = array())
	{
		$users = $this->_userids($value);

		$this->__set($key, array_diff($this->__get($key), $users));
	}

	/**
	 * Short description for 'iterate'
	 * Long description (if any) ...
	 *
	 * @param unknown $func Parameter description (if any) ...
	 * @param string $storage Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	static function iterate($func)
	{
		$db = &JFactory::getDBO();

		$query = "SELECT cn FROM #__courses;";

		$db->setQuery($query);

		$result = $db->loadResultArray();

		if ($result === false)
		{
			return false;
		}

		foreach ($result as $row)
		{
			call_user_func($func, $row);
		}

		return true;
	}

	/**
	 * Short description for 'exists'
	 * Long description (if any) ...
	 *
	 * @param unknown $course Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	static public function exists($course, $check_system = false)
	{
		$db = &JFactory::getDBO();

		if (empty($course))
		{
			return false;
		}

		if ($check_system)
		{
			if (is_numeric($course) && posix_getgrid($course))
			{
				return true;
			}
			
			if (!is_numeric($course) && posix_getgrnam($course))
			{
				return true;
			}
		}

		if (is_numeric($course))
		{
			$query = 'SELECT gidNumber FROM #__courses WHERE gidNumber=' . $db->Quote($course);
		}
		else
		{
			$query = 'SELECT gidNumber FROM #__courses WHERE cn=' . $db->Quote($course);
		}

		$db->setQuery($query);

		if (!$db->query())
		{
			return false;
		}

		if ($db->loadResult() > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Short description for 'find'
	 * Long description (if any) ...
	 *
	 * @param array $filters Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	static function find($filters = array())
	{
		$db = &JFactory::getDBO();

		// Type 0 - System Course
		// Type 1 - HUB Course
		// Type 2 - Project Course
		// Type 3 - Partner "Special" Course
		$gTypes = array('all', 'system', 'hub', 'project', 'partner', '0', '1', '2', '3');

		$types = !empty($filters['type']) ? $filters['type'] : array('all');

		foreach ($types as $type)
		{
			if (!in_array($type, $gTypes))
			{
				return false;
			}
		}

		if (in_array('all', $types))
		{
			$where_clause = '';
		}
		else
		{
			$t = implode(",", $types);
			if ($t == 'hub')
				$t = 1;
			if ($t == 'project')
				$t = 2;
			if ($t == 'partner')
				$t = 3;
			if ($t == 'system')
				$t = 0;
			$where_clause = 'WHERE type IN (' . $t . ')';
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			if ($where_clause != '')
			{
				$where_clause .= " AND";
			}
			else
			{
				$where_clause = "WHERE";
			}
			
			$where_clause .= " (LOWER(description) LIKE '%" . $filters['search'] . "%' OR LOWER(cn) LIKE '%" . $filters['search'] . "%')";
		}

		if (isset($filters['index']) && $filters['index'] != '')
		{
			if ($where_clause != '')
			{
				$where_clause .= " AND";
			}
			else
			{
				$where_clause = "WHERE";
			}
			
			$where_clause .= " (LOWER(description) LIKE '" . $filters['index'] . "%') ";
		}

		if (isset($filters['authorized']) && $filters['authorized'] === 'admin')
		{
			if (isset($filters['privacy']) && $filters['privacy'])
			{
				if ($where_clause != '')
				{
					$where_clause .= " AND";
				}
				else
				{
					$where_clause .= "WHERE";
				}

				switch ($filters['privacy'])
				{
					case 'private':
						$where_clause .= " privacy=4";
						break;
					case 'protected':
						$where_clause .= " privacy=1";
						break;
					case 'public':
						$where_clause .= " privacy=0";
						break;
				}
			}
			else
			{
				$where_clause .= "";
			}
		}
		else
		{
			if ($where_clause != '')
			{
				$where_clause .= " AND";
			}
			else
			{
				$where_clause .= "WHERE";
			}

			$where_clause .= " privacy=0";
		}

		if (isset($filters['policy']) && $filters['policy'])
		{
			if ($where_clause != '')
			{
				$where_clause .= " AND";
			}
			else
			{
				$where_clause .= "WHERE";
			}

			switch ($filters['policy'])
			{
				case 'closed':
					$where_clause .= " join_policy=3";
					break;
				case 'invite':
					$where_clause .= " join_policy=2";
					break;
				case 'restricted':
					$where_clause .= " join_policy=1";
					break;
				case 'open':
				default:
					$where_clause .= " join_policy=0";
					break;
			}
		}

		if (empty($filters['fields']))
		{
			$filters['fields'][] = 'cn';
		}

		$field = implode(',', $filters['fields']);

		$query = "SELECT $field FROM #__courses $where_clause";

		if (isset($filters['sortby']) && $filters['sortby'] != '')
		{
			$query .= " ORDER BY ";
			
			switch ($filters['sortby'])
			{
				case 'alias':
					$query .= 'cn ASC';
					break;
				case 'title':
					$query .= 'description ASC';
					break;
				default:
					$query .= $filters['sortby'];
					break;
			}
		}

		if (isset($filters['limit']) && $filters['limit'] != 'all')
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$query .= ";";

		$db->setQuery($query);

		if (!in_array('COUNT(*)', $filters['fields']))
		{
			$result = $db->loadObjectList();
		}
		else
		{
			$result = $db->loadResult();
		}

		if (empty($result))
		{
			return false;
		}

		return $result;
	}

	/**
	 * Short description for 'is_member_of'
	 * Long description (if any) ...
	 *
	 * @param string $table Parameter description (if any) ...
	 * @param unknown $uid Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function is_member_of($table, $uid)
	{
		if (!in_array($table, array('applicants', 'members', 'managers', 'invitees')))
		{
			return false;
		}
		
		if (!is_numeric($uid))
		{
			$uidNumber = JUserHelper::getUserId($uid);
		}
		else
		{
			$uidNumber = $uid;
		}
		
		return in_array($uidNumber, $this->get($table));
	}

	/**
	 * Short description for 'isMember'
	 * Long description (if any) ...
	 *
	 * @param unknown $uid Parameter description (if any) ...
	 * @return string Return description (if any) ...
	 */
	public function isMember($uid)
	{
		return $this->is_member_of('members', $uid);
	}

	/**
	 * Short description for 'isApplicant'
	 * Long description (if any) ...
	 *
	 * @param unknown $uid Parameter description (if any) ...
	 * @return string Return description (if any) ...
	 */
	public function isApplicant($uid)
	{
		return $this->is_member_of('applicants', $uid);
	}

	/**
	 * Short description for 'isManager'
	 * Long description (if any) ...
	 *
	 * @param unknown $uid Parameter description (if any) ...
	 * @return string Return description (if any) ...
	 */
	public function isManager($uid)
	{
		return $this->is_member_of('managers', $uid);
	}

	/**
	 * Short description for 'isInvitee'
	 * Long description (if any) ...
	 *
	 * @param unknown $uid Parameter description (if any) ...
	 * @return string Return description (if any) ...
	 */
	public function isInvitee($uid)
	{
		return $this->is_member_of('invitees', $uid);
	}

	/**
	 * Short description for 'getEmails'
	 * Long description (if any) ...
	 *
	 * @param string $key Parameter description (if any) ...
	 * @return array Return description (if any) ...
	 */
	public function getEmails($tbl = 'managers')
	{
		if (!in_array($tbl, array('applicants', 'members', 'managers', 'invitees')))
		{
			return false;
		}

		$db = JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		$query = 'SELECT u.email FROM #__users AS u, #__courses_' . $tbl . ' AS gm WHERE gm.gidNumber=' . $db->Quote($this->gidNumber) . ' AND u.id=gm.uidNumber;';

		$db->setQuery($query);

		$emails = $db->loadResultArray();

		return $emails;
	}

	/**
	 * Short description for 'search'
	 * Long description (if any) ...
	 *
	 * @param string $tbl Parameter description (if any) ...
	 * @param string $q Parameter description (if any) ...
	 * @return mixed Return description (if any) ...
	 */
	
	// @FIXME: next refactoring this might be getMembers(), getInvitees(), getApplicaants(), 
	// getManagers() with a filter and limit/offset option  *njk*
	public function search($tbl = '', $q = '')
	{
		if (!in_array($tbl, array('applicants', 'members', 'managers', 'invitees')))
		{
			return false;
		}

		$table = '#__courses_' . $tbl;

		$db = & JFactory::getDBO();

		$query = 'SELECT u.id FROM #__courses_ ' . $tbl . ' AS t,#__users AS u WHERE t.gidNumber=' . $db->Quote($this->gidNumber) . " AND u.id=t.uidNumber AND LOWER(u.name) LIKE '%" . strtolower($q) . "%';";

		$db->setQuery($query);

		return $db->loadResultArray();
	}

	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function offerings($index=null)
	{
		static $offerings;

		if (!$offerings)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'instance.php');
			$inst = new CoursesInstance(JFactory::getDBO()); //JTable::getInstance('instance', 'CoursesTable');
			$offerings = $inst->getCourseInstances(array(
				'course_cn' => $this->get('cn')
			));
		}

		if ($index !== null && is_numeric($index))
		{
			return (isset($offerings[$index]) ? $offerings[$index] : false);
		}
		return $offerings;
	}
}