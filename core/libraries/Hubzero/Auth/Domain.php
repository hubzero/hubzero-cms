<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Auth;

/**
 * Authentication domain
 */
class Domain
{
	/**
	 * Record ID
	 *
	 * @var  integer
	 */
	private $id;

	/**
	 * Domain type
	 *
	 * @var  string
	 */
	private $type;

	/**
	 * Authenticator name
	 *
	 * @var  string
	 */
	private $authenticator;

	/**
	 * Domain name
	 *
	 * @var  string
	 */
	private $domain;

	/**
	 * Parameters
	 *
	 * @var  string
	 */
	private $params;

	/**
	 * List of updated keys
	 *
	 * @var  array
	 */
	private $_updatedkeys = array();

	/**
	 * Update all fields?
	 *
	 * @var  boolean
	 */
	private $_updateAll = false;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
	}

	/**
	 * Clear any existing data
	 *
	 * @return  void
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

				$this->$key = null;
			}
		}

		$this->_updateAll = false;
		$this->_updatedkeys = array();
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
	 * Get a Domain instance
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @return  mixed   Object on success, False on failure
	 */
	public static function getInstance($type, $authenticator, $domain)
	{
		$hzad = new self();
		$hzad->type = $type;
		$hzad->authenticator = $authenticator;
		$hzad->domain = $domain;
		$hzad->read();

		if (!$hzad->id)
		{
			return false;
		}

		return $hzad;
	}

	/**
	 * Find a record by ID
	 *
	 * @param   integer  $id
	 * @return  mixed    Object on success, False on failure
	 */
	public static function find_by_id($id)
	{
		$hzad = new self();
		$hzad->id = $id;
		$hzad->read();

		if (empty($hzad->authenticator))
		{
			return false;
		}

		return $hzad;
	}

	/**
	 * Create a new instance and return it
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @return  mixed
	 */
	public function createInstance($type, $authenticator, $domain = null)
	{
		if (empty($type) || empty($authenticator))
		{
			return false;
		}

		$instance = new self();
		$instance->type = $type;
		$instance->authenticator = $authenticator;
		$instance->domain = $domain;
		$instance->create();

		if (!$instance->id)
		{
			return false;
		}

		return $instance;
	}

	/**
	 * Create a record
	 *
	 * @return  boolean  True on success, False on failure
	 */
	public function create()
	{
		$db =  \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "INSERT INTO `#__auth_domain` (id,type,authenticator,domain,params) VALUES ( " . $db->quote($this->id) .
				"," . $db->quote($this->type) . "," . $db->quote($this->authenticator) . "," . $db->quote($this->domain) . ","
				. $db->quote($this->params) . ");";

			$db->setQuery();

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062)
			{
				return true;
			}
		}
		else
		{
			$query = "INSERT INTO `#__auth_domain` (type,authenticator,domain,params) VALUES ( " .
				$db->quote($this->type) . "," . $db->quote($this->authenticator) . "," . $db->quote($this->domain) . "," . $db->quote($this->params) . ");";

			$db->setQuery($query);

			$result = $db->query();
			//var_dump($db);
			if ($result === false && $db->getErrorNum() == 1062)
			{
				$query = "SELECT id FROM `#__auth_domain` WHERE authenticator=" .
					$db->quote($this->authenticator) . " AND domain=" . $db->quote($this->domain) . " AND type=" . $db->quote($this->type) . ";";

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
	 * @return  boolean  True on success, False on failure
	 */
	public function read()
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "SELECT id,type,authenticator,domain,params FROM `#__auth_domain` WHERE id=" .
				$db->quote($this->id) . ";";
		}
		else
		{
			$query = "SELECT id,type,authenticator,domain,params FROM `#__auth_domain` WHERE "
				. " type=" . $db->quote($this->type)
				. " AND authenticator=" . $db->quote($this->authenticator)
				. " AND domain=" . $db->quote($this->domain)
				. ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (empty($result))
		{
			return false;
		}

		$this->clear();

		foreach ($result as $key=>$value)
		{
			$this->__set($key, $value);
		}

		$this->_updatedkeys = array();

		return true;
	}

	/**
	 * Update a record
	 *
	 * @param   boolean  $all  Update all values?
	 * @return  boolean  True on success, False on failure
	 */
	public function update($all = false)
	{
		$db =  \App::get('db');

		$query = "UPDATE `#__auth_domain` SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;

		foreach ($classvars as $property=>$value)
		{
			if (($property{0} == '_'))
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

		$db->setQuery($query);

		if (!empty($query))
		{
			$result = $db->query();

			if ($result === false)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @param   boolean  $deletelinks  Delete links?
	 * @return  boolean  True on success, False on failure
	 */
	public function delete($deletelinks = false)
	{
		if (!isset($this->toolname) && !isset($this->id))
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
			$db->setQuery("SELECT id FROM `#__auth_domain` WHERE `authenticator`=" . $db->quote($this->authenticator) . " AND `domain`=" . $db->quote($this->domain) . ";");

			$this->id = $db->loadResult();
		}

		if (empty($this->id))
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__auth_domain` WHERE `id`= " . $db->quote($this->id) . ";");

		if (!$db->query())
		{
			return false;
		}

		if ($deletelinks)
		{
			$db->setQuery("UPDATE `#__auth_links` SET `auth_domain_id`=NULL WHERE `auth_domain_id`=" . $db->quote($this->id) . ";");

			$db->query();
		}

		return true;
	}

	/**
	 * Retrieve the value of a property
	 *
	 * @param   string  $property  Property name
	 * @return  mixed
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
	 * Set a property
	 *
	 * @param   string  $property  Property name
	 * @param   mixed   $value     Value to set
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

		$this->$property = $value;

		if (!in_array($property, $this->_updatedkeys))
		{
			$this->_updatedkeys[] = $property;
		}
	}

	/**
	 * Check if a propety is set
	 *
	 * @param   string   $property  Property name
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
	 * Display an error
	 *
	 * @param   string   $message  Error message
	 * @param   integer  $level    Error level
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
	 * Fine a specific record, or create it
	 * if not found
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @return  mixed
	 */
	public static function find_or_create($type, $authenticator, $domain=null)
	{
		$hzad = new self();
		$hzad->type = $type;
		$hzad->authenticator = $authenticator;
		$hzad->domain = $domain;
		$hzad->read();

		if (empty($hzad->id) && !$hzad->create())
		{
			return false;
		}

		return $hzad;
	}
}

