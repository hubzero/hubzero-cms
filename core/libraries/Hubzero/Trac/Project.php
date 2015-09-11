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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Trac;

/**
 * TRAC project class
 */
class Project
{
	/**
	 * Project ID
	 *
	 * @var  integer
	 */
	private $id = null;

	/**
	 * Project name
	 *
	 * @var  string
	 */
	private $name = null;

	/**
	 * List of updated keys
	 *
	 * @var  array
	 */
	private $_updatedkeys = array();

	/**
	 * List keys
	 *
	 * @var  array
	 */
	private $_list_keys = array();

	/**
	 * Resets internal properties
	 *
	 * @return  boolean
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
	 * @param   string  $msg  Message to log
	 * @return  void
	 */
	private function logDebug($msg)
	{
		$xlog = \App::get('log')->logger('debug');
		$xlog->debug($msg);
	}

	/**
	 * Output data as an array
	 *
	 * @return  array
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
	 * @param   string  $name  Project name
	 * @return  mixed
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
	 * @param   string  $name  Project name
	 * @return  mixed
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
	 * Create a record
	 *
	 * @return  boolean
	 */
	public function create()
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "INSERT INTO `#__trac_project` (id,name) VALUES ( " . $db->quote($this->id) . "," . $db->quote($this->name) . ");";
			$db->setQuery($query);

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062)
			{
				return true;
			}
		}
		else
		{
			$query = "INSERT INTO `#__trac_project` (name) VALUES ( " . $db->quote($this->name) . ");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() == 1062)
			{
				$query = "SELECT id FROM `#__trac_project` WHERE name=" . $db->quote($this->name) . ";";

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
	 * Read a record
	 *
	 * @return  boolean
	 */
	public function read()
	{
		$db = \App::get('db');

		$lazyloading = false;

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "SELECT * FROM `#__trac_project` WHERE id = " . $db->quote($this->id) . ";";
		}
		else
		{
			$query = "SELECT * FROM `#__trac_project` WHERE name = " . $db->quote($this->name) . ";";
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
	 * Update a record
	 *
	 * @param   boolean  $all
	 * @return  boolean
	 */
	public function update($all = false)
	{
		$db =  \App::get('db');

		$query = "UPDATE `#__trac_project` SET ";

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
				$query .= "`$property`=" . $db->quote($value);
			}
		}

		$query .= " WHERE `id`=" . $db->quote($this->__get('id')) . ";";

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
	 * Delete a record
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		if (!isset($this->name) && !isset($this->id))
		{
			return false;
		}

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (!isset($this->id))
		{
			$db->setQuery("SELECT id FROM `#__trac_project` WHERE name=" . $db->quote($this->name) . ";");

			$this->id = $db->loadResult();
		}

		if (empty($this->id))
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__trac_project` WHERE id=" . $db->quote($this->id) . ";");

		if (!$db->query())
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__trac_user_permission` WHERE trac_project_id=" . $db->quote($this->id) . ";");
		$db->query();
		$db->setQuery("DELETE FROM `#__trac_group_permission` WHERE trac_project_id=" . $db->quote($this->id) . ";");
		$db->query();

		return true;
	}

	/**
	 * Property accessor
	 *
	 * @param   string  $property
	 * @return  string
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

		if (in_array($property, $this->_list_keys))
		{
			if (!array_key_exists($property, get_object_vars($this)))
			{
				$db =  \App::get('db');

				if (is_object($db))
				{
					$query = null;

					if (!empty($query))
					{
						$db->setQuery($query);

						$result = $db->loadColumn();
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
	 * Property setter
	 *
	 * @param   string  $property
	 * @param   mixed   $value
	 * @return  void
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
	 * Check if a property is set
	 *
	 * @param   string  $property  Property name
	 * @return  boolean
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
	 * Unset a property
	 *
	 * @param   string  $property  Property name
	 * @return  void
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
	 * Echo an error message
	 *
	 * @param   string   $message
	 * @param   integer  $level
	 * @return  void
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
	 * Property accessor
	 *
	 * @param   string  $property
	 * @return  string
	 */
	public function get($key)
	{
		return $this->__get($key);
	}

	/**
	 * Property setter
	 *
	 * @param   string  $property
	 * @param   mixed   $value
	 * @return  void
	 */
	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	/**
	 * Add permission to a user
	 *
	 * @param   string  $user
	 * @param   string  $action
	 * @return  void
	 */
	public function add_user_permission($user, $action)
	{
		$db =  \App::get('db');

		if ($user == 'anonymous')
		{
			$user = '0';
		}

		if (!is_numeric($user))
		{
			$query = "SELECT id FROM `#__users` WHERE username=" . $db->quote($user) . ";";
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

		$quoted_project_id = $db->quote($this->id);
		$quoted_user_id = $db->quote($user_id);
		$values = '';

		foreach ((array) $action as $a)
		{
			if (!empty($values))
			{
				$values .= ',';
			}
			$values .= "($quoted_project_id,$quoted_user_id," . $db->quote($a) .")";
		}

		$query = "INSERT IGNORE INTO `#__trac_user_permission` (trac_project_id,user_id,action) VALUES " .  $values . ";";

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Add permission to a group
	 *
	 * @param   string  $group
	 * @param   string  $action
	 * @return  void
	 */
	public function add_group_permission($group, $action)
	{
		$db =  \App::get('db');

		if ($group == 'authenticated')
		{
			$group = '0';
		}

		if (!is_numeric($group))
		{
			$query = "SELECT gidNumber FROM `#__xgroups` WHERE cn=" . $db->quote($group) . ";";
			$db->setQuery($query);
			$group_id = $db->loadResult();

			if ($group_id === false)
			{
				$this->_error("Unknown group $group");
				return false;
			}
		}

		$quoted_project_id = $db->quote($this->id);
		$quoted_group_id = $db->quote($group_id);
		$values = '';

		foreach ((array) $action as $a)
		{
			if (!empty($values))
			{
				$values .= ',';
			}
			$values .= "($quoted_project_id,$quoted_group_id," . $db->quote($a) .")";
		}

		$query = "INSERT IGNORE INTO `#__trac_group_permission` (trac_project_id,group_id,action) VALUES " .  $values . ";";

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Remove permission form a user
	 *
	 * @param   string  $user
	 * @param   string  $action
	 * @return  void
	 */
	public function remove_user_permission($user, $action)
	{
		$db =  \App::get('db');
		$all = false;

		if ($user == 'anonymous')
		{
			$user = '0';
		}

		if (!is_numeric($user))
		{
			$query = "SELECT id FROM `#__users` WHERE username=" . $db->quote($user) . ";";
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

		$quoted_project_id = $db->quote($this->id);
		$quoted_user_id = $db->quote($user_id);
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
			$values .= $db->quote($a);
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
	 * Remove permission form a group
	 *
	 * @param   string  $group
	 * @param   string  $action
	 * @return  void
	 */
	public function remove_group_permission($group, $action)
	{
		$db =  \App::get('db');
		$all = false;

		if ($group == 'authenticated')
		{
			$group = '0';
		}

		if (!is_numeric($group))
		{
			$query = "SELECT gidNumber FROM `#__xgroups` WHERE cn=" . $db->quote($group) . ";";
			$db->setQuery($query);
			$group_id = $db->loadResult();

			if ($group_id === null)
			{
				$this->_error("Unknown group $group");
				return false;
			}
		}

		$quoted_project_id = $db->quote($this->id);
		$quoted_group_id = $db->quote($group_id);
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
			$values .= $db->quote($a);
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
	 * Get permissions for a user
	 *
	 * @param   string  $user
	 * @return  array
	 */
	public function get_user_permission($user)
	{
		$db =  \App::get('db');
		$quoted_project_id = $db->quote($this->id);

		if ($user == "anonymous")
		{
			$user = '0';
		}
		$quoted_user = $db->quote($user);
		if (is_numeric($user))
		{
			$query = "SELECT action FROM `#__trac_user_permission` AS up WHERE up.trac_project_id=$quoted_project_id AND up.user_id=$quoted_user;";
		}
		else
		{
			$query = "SELECT action FROM `#__trac_user_permission` AS up, `#__users` AS u WHERE up.trac_project_id=$quoted_project_id AND u.id=up.user_id AND u.username=$quoted_user;";
		}

		$db->setQuery($query);
		$result = $db->loadColumn();

		return $result;
	}

	/**
	 * Get permissions for a group
	 *
	 * @param   string  $group
	 * @return  array
	 */
	public function get_group_permission($group)
	{
		$db =  \App::get('db');
		$quoted_project_id = $db->quote($this->id);

		if ($group == 'authenticated')
		{
			$group = '0';
		}
		$quoted_group = $db->quote($group);
		if (is_numeric($group))
		{
			$query = "SELECT action FROM `#__trac_group_permission` AS gp WHERE gp.trac_project_id=$quoted_project_id AND gp.group_id=$quoted_group;";
		}
		else
		{
			$query = "SELECT action FROM `#__trac_group_permission` AS gp, `#__xgroups` AS g WHERE gp.trac_project_id=$quoted_project_id AND g.gidNumber=gp.group_id AND g.cn=$quoted_group;";
		}

		$db->setQuery($query);
		$result = $db->loadColumn();

		return $result;
	}
}

