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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Auth;

/**
 * Authentication Link
 */
class Link
{
	/**
	 * Record ID
	 *
	 * @var  integer
	 */
	private $id;

	/**
	 * User ID
	 *
	 * @var  integer
	 */
	private $user_id;

	/**
	 * Domain ID
	 *
	 * @var  integer
	 */
	private $auth_domain_id;

	/**
	 * Username
	 *
	 * @var  string
	 */
	private $username;

	/**
	 * User email
	 *
	 * @var  string
	 */
	private $email;

	/**
	 * User password
	 *
	 * @var  string
	 */
	private $password;

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
	 * Get an instance of a record
	 *
	 * @param   integer  $auth_domain_id
	 * @param   string   $username
	 * @return  mixed    Object on success, False on failure
	 */
	public static function getInstance($auth_domain_id, $username)
	{
		$hzal = new self();
		$hzal->auth_domain_id = $auth_domain_id;
		$hzal->username = $username;

		$hzal->read();

		if (!$hzal->id)
		{
			return false;
		}

		return $hzal;
	}

	/**
	 * Find a record by ID
	 *
	 * @param   integer  $id
	 * @return  mixed    Object on success, False on failure
	 */
	public static function find_by_id($id)
	{
		$hzal = new self();
		$hzal->id = $id;

		$hzal->read();

		if (empty($hzal->auth_domain_id))
		{
			return false;
		}

		return $hzal;
	}

	/**
	 * Create a new instance and return it
	 *
	 * @param   integer  $auth_domain_id
	 * @param   string   $username
	 * @return  mixed
	 */
	public function createInstance($auth_domain_id, $username)
	{
		if (empty($auth_domain_id) || empty($username))
		{
			return false;
		}

		$instance = new self();

		$instance->auth_domain_id = $auth_domain_id;
		$instance->username = $username;

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
			$query = "INSERT INTO `#__auth_link` (id,user_id,auth_domain_id,username,email,password,params) VALUES ( "
				. $db->quote($this->id) .
				"," . $db->quote($this->user_id) .
				"," . $db->quote($this->auth_domain_id) .
				"," . $db->quote($this->username) .
				"," . $db->quote($this->email) .
				"," . $db->quote($this->password) .
				"," . $db->quote($this->params) .
				");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062)
			{
				return true;
			}
		}
		else
		{
			$query = "INSERT INTO `#__auth_link` (user_id,auth_domain_id,username,email,password,params) VALUES ( "
				. $db->quote($this->user_id) .
				"," . $db->quote($this->auth_domain_id) .
				"," . $db->quote($this->username) .
				"," . $db->quote($this->email) .
				"," . $db->quote($this->password) .
				"," . $db->quote($this->params) .
				");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() == 1062)
			{
				$query = "SELECT id FROM `#__auth_link` WHERE " .
					"auth_domain_id=" . $db->quote($this->auth_domain_id) . " AND " .
					"user_id=" . $db->quote($this->user_id) . ";";

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

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM `#__auth_link` WHERE id=" .
				$db->quote($this->id) . ";";
		}
		else if (is_numeric($this->user_id))
		{
			$query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM `#__auth_link` WHERE " .
				" user_id=" . $db->quote($this->user_id) . " AND auth_domain_id=" . $db->quote($this->auth_domain_id) . ";";
		}
		else if (is_string($this->username))
		{
			$query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM `#__auth_link` WHERE " .
				" username=" . $db->quote($this->username) . " AND auth_domain_id=" . $db->quote($this->auth_domain_id) . ";";

		}

		if (empty($query))
		{
			return false;
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
	 * @param   boolean  $all  Update all properties?
	 * @return  boolean
	 */
	public function update($all = false)
	{
		$db =  \App::get('db');

		$query = "UPDATE `#__auth_link` SET ";

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
	 * @return  boolean
	 */
	public function delete()
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (!isset($this->id))
		{
			$db->setQuery("SELECT id FROM `#__auth_link` WHERE user_id=" . $db->quote($this->user_id) . " AND auth_domain_id=" . $db->quote($this->auth_domain_id) . ";");

			$this->id = $db->loadResult();
		}

		if (empty($this->id))
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__auth_link` WHERE id= " . $db->quote($this->id) . ";");

		if (!$db->query())
		{
			return false;
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
	 * Return array of linked accounts associated with a given user id
	 * Also include auth domain name for easy display of domain name
	 *
	 * @param   integer  $user_id  ID of user to return accounts for
	 * @return  array    Array of auth link entries for the given user_id
	 */
	public static function find_by_user_id($user_id = null)
	{
		if (empty($user_id))
		{
			return false;
		}

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$sql  = "SELECT l.*, d.authenticator as auth_domain_name FROM `#__auth_link` as l, `#__auth_domain` as d";
		$sql .= " WHERE l.auth_domain_id = d.id AND l.user_id = " . $db->quote($user_id);

		$db->setQuery($sql);

		$result = $db->loadAssocList();

		if (empty($result))
		{
			return false;
		}

		return $result;
	}

	/**
	 * Return array of linked accounts associated with a given email address
	 * Also include auth domain name for easy display of domain name
	 *
	 * @param   string  $email  Email address to match accounts against
	 * @return  array   Array of auth link entries for the given user_id
	 */
	public static function find_by_email($email = null, $exclude = array())
	{
		if (empty($email))
		{
			return false;
		}

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$sql  = "SELECT l.*, d.authenticator as auth_domain_name FROM `#__auth_link` as l, `#__auth_domain` as d";
		$sql .= " WHERE l.auth_domain_id = d.id AND l.email = " . $db->quote($email);

		if (!empty($exclude[0]))
		{
			foreach ($exclude as $e)
			{
				$sql .= " AND l.auth_domain_id != " . $db->quote($e);
			}
		}

		$db->setQuery($sql);

		$result = $db->loadAssocList();

		if (empty($result))
		{
			return false;
		}

		return $result;
	}

	/**
	 * Delete a record by User ID
	 *
	 * @param   integer  $uid  User ID
	 * @return  boolean
	 */
	public static function delete_by_user_id($uid = null)
	{
		if (empty($uid))
		{
			return true;
		}

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__auth_link` WHERE `user_id`= " . $db->quote($uid) . ";");

		if (!$db->query())
		{
			return false;
		}

		return true;
	}

	/**
	 * Find existing auth_link entry, return false if none exists
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @param   string  $username
	 * @return  mixed   object on success and false on failure
	 */
	public static function find($type, $authenticator, $domain, $username)
	{
		$hzad = Domain::find_or_create($type,$authenticator,$domain);

		if (!is_object($hzad))
		{
			return false;
		}

		if (empty($username))
		{
			return false;
		}

		$hzal = new self();
		$hzal->username = $username;
		$hzal->auth_domain_id = $hzad->id;

		$hzal->read();

		if (empty($hzal->id))
		{
			return false;
		}

		return $hzal;
	}

	/**
	 * Find a record, creating it if not found.
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @param   string  $username
	 * @return  mixed   Object on success, False on failure
	 */
	public static function find_or_create($type, $authenticator, $domain, $username)
	{
		$hzad = Domain::find_or_create($type, $authenticator, $domain);

		if (!is_object($hzad))
		{
			return false;
		}

		if (empty($username))
		{
			return false;
		}

		$hzal = new self();
		$hzal->username = $username;
		$hzal->auth_domain_id = $hzad->id;

		$hzal->read();

		if (empty($hzal->id) && !$hzal->create())
		{
			return false;
		}

		return $hzal;
	}

	/**
	 * Find trusted emails by User ID
	 *
	 * @param   integer  $user_id  USer ID
	 * @return  mixed
	 */
	public function find_trusted_emails($user_id)
	{
		if (empty($user_id) || !is_numeric($user_id))
		{
			return false;
		}

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$sql = "SELECT email FROM `#__auth_link` WHERE `user_id` = " . $db->quote($user_id) . ";";

		$db->setQuery($sql);

		$result = $db->loadColumn();

		if (empty($result))
		{
			return false;
		}

		return $result;
	}
}

