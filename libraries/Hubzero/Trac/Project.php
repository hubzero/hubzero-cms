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

namespace Hubzero\Trac;

/**
 * TRAC project class
 */
class Project
{
	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	private $id = null;

	/**
	 * Description for 'name'
	 *
	 * @var unknown
	 */
	private $name = null;

	/**
	 * Description for '_updatedkeys'
	 *
	 * @var array
	 */
	private $_updatedkeys = array();

	/**
	 * Description for '_list_keys'
	 *
	 * @var array
	 */
	private $_list_keys = array();

	/**
	 * Resets internal properties
	 *
	 * @return     boolean
	 */
	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);

		$this->_updatedkeys = array();

		foreach ($cvars as $key => $value)
		{
			if ($key{0} != '_')
			{
				unset($this->$key);

				if (!in_array($key, $this->_list_keys))
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
	 * Log a debug message
	 *
	 * @param      string $msg Message to log
	 * @return     void
	 */
	private function logDebug($msg)
	{
		$xlog =  \JFactory::getLogger();
		$xlog->debug($msg);
	}

	/**
	 * Output data as an array
	 *
	 * @return     array
	 */
	public function toArray()
	{
		$result = array();

		$cvars = get_class_vars(__CLASS__);

		foreach ($cvars as $key=>$value)
		{
			if ($key{0} == '_')
			{
				continue;
			}

			$current = $this->__get($key);

			$result[$key] = $current;
		}

		return $result;
	}

	/**
	 * Find a project
	 *
	 * @param      string $name PRoject name
	 * @return     mixed
	 */
	public function find($name)
	{
		$hztp = new self();

		if (is_numeric($name))
		{
			$hztp->id = $name;
		}
		else
		{
			$hztp->name = $name;
		}

		if ($hztp->read() == false)
		{
			return false;
		}

		return $hztp;
	}

	/**
	 * Find a project. Create it if one doesn't exist.
	 *
	 * @param      string $name Project name
	 * @return     mixed
	 */
	public static function find_or_create($name)
	{
		$hztp = new self();

		if (is_numeric($name))
		{
			$hztp->id = $name;
		}
		else
		{
			$hztp->name = $name;
		}

		if ($hztp->read() == false)
		{
			if ($hztp->create() == false)
			{
				return false;
			}
		}

		return $hztp;
	}

	/**
	 * Short description for 'create'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function create()
	{
		$db = \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "INSERT INTO `#__trac_project` (id,name) VALUES ( " . $db->Quote($this->id) . "," . $db->Quote($this->name) . ");";
			$db->setQuery($query);

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062)
			{
				return true;
			}
		}
		else
		{
			$query = "INSERT INTO `#__trac_project` (name) VALUES ( " . $db->Quote($this->name) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() == 1062)
			{
				$query = "SELECT id FROM `#__trac_project` WHERE name=" . $db->Quote($this->name) . ";";

				$db->setQuery($query);

				$result = $db->loadResult();

				if ($result == null)
				{
					return false;
				}

				$this->id = $result;
				return true;
			}
			else if ($result !== false)
			{
				$this->id = $db->insertid();
				return true;
			}
		}

		return false;
	}

	/**
	 * Short description for 'read'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function read()
	{
		$db = \JFactory::getDBO();

		$lazyloading = false;

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "SELECT * FROM `#__trac_project` WHERE id = " . $db->Quote($this->id) . ";";
		}
		else
		{
			$query = "SELECT * FROM `#__trac_project` WHERE name = " . $db->Quote($this->name) . ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result))
		{
			return false;
		}

		$this->clear();

		foreach ($result as $key => $value)
		{
			if (property_exists(__CLASS__, $key) && $key{0} != '_')
			{
				$this->__set($key, $value);
			}
		}

		$this->_updatedkeys = array();

		return true;
	}

	/**
	 * Short description for 'update'
	 *
	 * Long description (if any) ...
	 *
	 * @param      boolean $all Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function update($all = false)
	{
		$db =  \JFactory::getDBO();

		$query = "UPDATE #__trac_project SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;

		foreach ($classvars as $property=>$value)
		{
			if (($property{0} == '_') || in_array($property, $this->_list_keys))
			{
				continue;
			}

			if (!$all && !in_array($property, $this->_updatedkeys))
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

			$value = $this->__get($property);

			if ($value === null)
			{
				$query .= "`$property`=NULL";
			}
			else
			{
				$query .= "`$property`=" . $db->Quote($value);
			}
		}

		$query .= " WHERE `id`=" . $db->Quote($this->__get('id')) . ";";

		if ($first == true)
		{
			$query = '';
		}

		if (!empty($query))
		{
			$db->setQuery($query);

			$result = $db->query();

			if ($result === false)
			{
				return false;
			}

			$affected = $db->getAffectedRows();

			if ($affected < 1)
			{
				$this->create();

				$db->setQuery($query);

				$result = $db->query();

				if ($result === false)
				{
					return false;
				}

				$affected = $db->getAffectedRows();

				if ($affected < 1)
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Short description for 'delete'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function delete()
	{
		if (!isset($this->name) && !isset($this->id))
		{
			return false;
		}

		$db = \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (!isset($this->id))
		{
			$db->setQuery("SELECT id FROM `#__trac_project` WHERE name=" . $db->Quote($this->name) . ";");

			$this->id = $db->loadResult();
		}

		if (empty($this->id))
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__trac_project` WHERE id=" . $db->Quote($this->id) . ";");

		if (!$db->query())
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__trac_user_permission` WHERE trac_project_id=" . $db->Quote($this->id) . ";");
		$db->query();
		$db->setQuery("DELETE FROM `#__trac_group_permission` WHERE trac_project_id=" . $db->Quote($this->id) . ";");
		$db->query();

		return true;
	}

	/**
	 * Short description for '__get'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $property Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function __get($property = null)
	{
		$xlog =  \JFactory::getLogger();

		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}

			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}

		if (in_array($property, $this->_list_keys))
		{
			if (!array_key_exists($property, get_object_vars($this)))
			{
				$db =  \JFactory::getDBO();

				if (is_object($db))
				{
					$query = null;

					if (!empty($query))
					{
						$db->setQuery($query);

						$result = $db->loadResultArray();
					}

					if ($result !== false)
					{
						$this->__set($property, $result);
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
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
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

		if (in_array($property, $this->_list_keys))
		{
			$value = array_diff((array) $value, array(''));
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
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $property Parameter description (if any) ...
	 * @return     string Return description (if any) ...
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
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $property Parameter description (if any) ...
	 * @return     void
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
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $message Parameter description (if any) ...
	 * @param      integer $level Parameter description (if any) ...
	 * @return     void
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
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $key Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function get($key)
	{
		return $this->__get($key);
	}

	/**
	 * Short description for 'set'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	/**
	 * Short description for 'add_user_permission'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $user Parameter description (if any) ...
	 * @param      unknown $action Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function add_user_permission($user,$action)
	{
		$db =  \JFactory::getDBO();

		if ($user == 'anonymous')
		{
			$user = '0';
		}

		if (!is_numeric($user))
		{
			$query = "SELECT id FROM `#__users` WHERE username=" . $db->Quote($user) . ";";
			$db->setQuery($query);
			$user_id = $db->loadResult();

			if ($user_id === false)
			{
				$this->_error("Unknown user $user");
				return false;
			}
		}
		else
		{
			$user_id = $user;
		}

		$quoted_project_id = $db->Quote($this->id);
		$quoted_user_id = $db->Quote($user_id);
		$values = '';

		foreach ((array) $action as $a)
		{
			if (!empty($values))
			{
				$values .= ',';
			}
			$values .= "($quoted_project_id,$quoted_user_id," . $db->Quote($a) .")";
		}

		$query = "INSERT IGNORE INTO `#__trac_user_permission` (trac_project_id,user_id,action) VALUES " .  $values . ";";

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Short description for 'add_group_permission'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $group Parameter description (if any) ...
	 * @param      unknown $action Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function add_group_permission($group,$action)
	{
		$db =  \JFactory::getDBO();

		if ($group == 'authenticated')
		{
			$group = '0';
		}

		if (!is_numeric($group))
		{
			$query = "SELECT gidNumber FROM `#__xgroups` WHERE cn=" . $db->Quote($group) . ";";
			$db->setQuery($query);
			$group_id = $db->loadResult();

			if ($group_id === false)
			{
				$this->_error("Unknown group $group");
				return false;
			}
		}

		$quoted_project_id = $db->Quote($this->id);
		$quoted_group_id = $db->Quote($group_id);
		$values = '';

		foreach ((array) $action as $a)
		{
			if (!empty($values))
			{
				$values .= ',';
			}
			$values .= "($quoted_project_id,$quoted_group_id," . $db->Quote($a) .")";
		}

		$query = "INSERT IGNORE INTO `#__trac_group_permission` (trac_project_id,group_id,action) VALUES " .  $values . ";";

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Short description for 'remove_user_permission'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $user Parameter description (if any) ...
	 * @param      unknown $action Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function remove_user_permission($user,$action)
	{
		$db =  \JFactory::getDBO();
		$all = false;

		if ($user == 'anonymous')
		{
			$user = '0';
		}

		if (!is_numeric($user))
		{
			$query = "SELECT id FROM `#__users` WHERE username=" . $db->Quote($user) . ";";
			$db->setQuery($query);
			$user_id = $db->loadResult();

			if ($user_id === false)
			{
				$this->_error("Unknown user $user");
				return false;
			}
		}
		else
		{
			$user_id = $user;
		}

		$quoted_project_id = $db->Quote($this->id);
		$quoted_user_id = $db->Quote($user_id);
		$values = '';

		foreach ((array) $action as $a)
		{
			if ($a == '*')
			{
				$all = true;
			}
			if (!empty($values))
			{
				$values .= ',';
			}
			$values .= $db->Quote($a);
		}

		$query = "DELETE FROM `#__trac_user_permission` WHERE trac_project_id=$quoted_project_id AND user_id=$quoted_user_id";

		if (!$all)
		{
			$query .= " AND action IN (" .  $values . ");";
		}

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Short description for 'remove_group_permission'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $group Parameter description (if any) ...
	 * @param      unknown $action Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function remove_group_permission($group,$action)
	{
		$db =  \JFactory::getDBO();
		$all = false;

		if ($group == 'authenticated')
		{
			$group = '0';
		}

		if (!is_numeric($group))
		{
			$query = "SELECT gidNumber FROM `#__xgroups` WHERE cn=" . $db->Quote($group) . ";";
			$db->setQuery($query);
			$group_id = $db->loadResult();

			if ($group_id === null)
			{
				$this->_error("Unknown group $group");
				return false;
			}
		}

		$quoted_project_id = $db->Quote($this->id);
		$quoted_group_id = $db->Quote($group_id);
		$values = '';

		foreach ((array) $action as $a)
		{
			if ($a == '*')
			{
				$all = true;
			}
			if (!empty($values))
			{
				$values .= ',';
			}
			$values .= $db->Quote($a);
		}

		$query = "DELETE FROM `#__trac_group_permission` WHERE trac_project_id=$quoted_project_id AND group_id=$quoted_group_id";

		if (!$all)
		{
			$query .= " AND action IN (" .  $values . ");";
		}

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Short description for 'get_user_permission'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $user Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function get_user_permission($user)
	{
		$db =  \JFactory::getDBO();
		$quoted_project_id = $db->Quote($this->id);

		if ($user == "anonymous")
		{
			$user = '0';
		}
		$quoted_user = $db->Quote($user);
		if (is_numeric($user))
		{
			$query = "SELECT action FROM `#__trac_user_permission` AS up WHERE up.trac_project_id=$quoted_project_id AND up.user_id=$quoted_user;";
		}
		else
		{
			$query = "SELECT action FROM `#__trac_user_permission` AS up, `#__users` AS u WHERE up.trac_project_id=$quoted_project_id AND u.id=up.user_id AND u.username=$quoted_user;";
		}

		$db->setQuery($query);
		$result = $db->loadResultArray();

		return $result;
	}

	/**
	 * Short description for 'get_group_permission'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $group Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function get_group_permission($group)
	{
		$db =  \JFactory::getDBO();
		$quoted_project_id = $db->Quote($this->id);

		if ($group == 'authenticated')
		{
			$group = '0';
		}
		$quoted_group = $db->Quote($group);
		if (is_numeric($group))
		{
			$query = "SELECT action FROM `#__trac_group_permission` AS gp WHERE gp.trac_project_id=$quoted_project_id AND gp.group_id=$quoted_group;";
		}
		else
		{
			$query = "SELECT action FROM `#__trac_group_permission` AS gp, `#__xgroups` AS g WHERE gp.trac_project_id=$quoted_project_id AND g.gidNumber=gp.group_id AND g.cn=$quoted_group;";
		}

		$db->setQuery($query);
		$result = $db->loadResultArray();

		return $result;
	}
}

