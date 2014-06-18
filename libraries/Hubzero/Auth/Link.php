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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Auth;

/**
 * Short description for 'Hubzero\Auth\Link'
 *
 * Long description (if any) ...
 */
class Link
{

	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	private $id;

	/**
	 * Description for 'user_id'
	 *
	 * @var unknown
	 */
	private $user_id;

	/**
	 * Description for 'auth_domain_id'
	 *
	 * @var unknown
	 */
	private $auth_domain_id;

	/**
	 * Description for 'username'
	 *
	 * @var unknown
	 */
	private $username;

	/**
	 * Description for 'email'
	 *
	 * @var unknown
	 */
	private $email;

	/**
	 * Description for 'password'
	 *
	 * @var unknown
	 */
	private $password;

	/**
	 * Description for 'params'
	 *
	 * @var unknown
	 */
	private $params;

	/**
	 * Description for '_updatedkeys'
	 *
	 * @var array
	 */
	private $_updatedkeys = array();

	/**
	 * Description for '_updateAll'
	 *
	 * @var boolean
	 */
	private $_updateAll = false;

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @return	 void
	 */
	private function __construct()
	{
	}

	/**
	 * Short description for 'clear'
	 *
	 * Long description (if any) ...
	 *
	 * @return	 void
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
	 * Short description for 'logDebug'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $msg Parameter description (if any) ...
	 * @return	 void
	 */
	private function logDebug($msg)
	{
		$xlog =  \JFactory::getLogger();
		$xlog->debug($msg);
	}

	/**
	 * Short description for 'getInstance'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $auth_domain_id Parameter description (if any) ...
	 * @param	  unknown $username Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
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
	 * Short description for 'find_by_id'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $id Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	public static function find_by_id($id)
	{
	$hzal = new self();
	$hzal->id = $id;

		$hzal->read();

		if (empty($hzal->auth_domain_id))
		return false;

		return $hzal;

	}

	/**
	 * Short description for 'createInstance'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $auth_domain_id Parameter description (if any) ...
	 * @param	  unknown $username Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	public function createInstance($auth_domain_id,$username)
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
	 * Short description for 'create'
	 *
	 * Long description (if any) ...
	 *
	 * @return	 boolean Return description (if any) ...
	 */
	public function create()
	{
		$db =  \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "INSERT INTO #__auth_link (id,user_id,auth_domain_id,username,email,password,params) VALUES ( "
				. $db->Quote($this->id) .
				"," . $db->Quote($this->user_id) .
				"," . $db->Quote($this->auth_domain_id) .
				"," . $db->Quote($this->username) .
				"," . $db->Quote($this->email) .
				"," . $db->Quote($this->password) .
				"," . $db->Quote($this->params) .
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
			$query = "INSERT INTO #__auth_link (user_id,auth_domain_id,username,email,password,params) VALUES ( "
				. $db->Quote($this->user_id) .
				"," . $db->Quote($this->auth_domain_id) .
				"," . $db->Quote($this->username) .
				"," . $db->Quote($this->email) .
				"," . $db->Quote($this->password) .
				"," . $db->Quote($this->params) .
				");";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false && $db->getErrorNum() == 1062)
			{
				$query = "SELECT id FROM #__auth_link WHERE " .
					"auth_domain_id=" . $db->Quote($this->auth_domain_id) . " AND " .
					"user_id=" . $db->Quote($this->user_id) . ";";

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
	 * @return	 boolean Return description (if any) ...
	 */
	public function read()
	{
		$db = \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM #__auth_link WHERE id=" .
				$db->Quote($this->id) . ";";
		}
		else if (is_numeric($this->user_id))
		{
			$query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM #__auth_link WHERE " .
				" user_id=" . $db->Quote($this->user_id) . " AND auth_domain_id=" . $db->Quote($this->auth_domain_id) . ";";
		}
		else if (is_string($this->username))
		{
			$query = "SELECT id,user_id,auth_domain_id,username,email,password,params FROM #__auth_link WHERE " .
				" username=" . $db->Quote($this->username) . " AND auth_domain_id=" . $db->Quote($this->auth_domain_id) . ";";

		}

		if (empty($query)) {
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
	 * Short description for 'update'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  boolean $all Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	function update($all = false)
	{
		$db =  \JFactory::getDBO();

		$query = "UPDATE #__auth_link SET ";

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
				$query .= "`$property`=" . $db->Quote($value);
			}
		}

		$query .= " WHERE `id`=" . $db->Quote($this->__get('id')) . ";";

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
	 * Short description for 'delete'
	 *
	 * Long description (if any) ...
	 *
	 * @return	 boolean Return description (if any) ...
	 */
	public function delete()
	{
		$db = \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		if (!isset($this->id))
		{
			$db->setQuery("SELECT id FROM #__auth_link WHERE user_id=" .
				$db->Quote($this->user_id) . " AND auth_domain_id=" . $db->Quote($this->auth_domain_id) . ";");

			$this->id = $db->loadResult();
		}

		if (empty($this->id))
		{
			return false;
		}

		$db->setQuery("DELETE FROM #__auth_link WHERE id= " . $db->Quote($this->id) . ";");

		if (!$db->query())
		{
			return false;
		}

		return true;
	}

	/**
	 * Short description for '__get'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  string $property Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
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
	 * Short description for '__set'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  string $property Parameter description (if any) ...
	 * @param	  unknown $value Parameter description (if any) ...
	 * @return	 void
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
			$this->_updatedkeys[] = $property;
	}

	/**
	 * Short description for '__isset'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  string $property Parameter description (if any) ...
	 * @return	 string Return description (if any) ...
	 */
	public function __isset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
				$property = '(null)';

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
	 * @param	  string $property Parameter description (if any) ...
	 * @return	 void
	 */
	public function __unset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
				$property = '(null)';

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
	 * @param	  string $message Parameter description (if any) ...
	 * @param	  integer $level Parameter description (if any) ...
	 * @return	 void
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
	 * @param	  int $user_id - id of user to return accounts for
	 * @return	 array Return - array of auth link entries for the given user_id
	 */
	public static function find_by_user_id($user_id = null)
	{
		if (empty($user_id))
		{
			return false;
		}

		$db = \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		$sql  = "SELECT l.*, d.authenticator as auth_domain_name FROM #__auth_link as l, #__auth_domain as d";
		$sql .= " WHERE l.auth_domain_id = d.id AND l.user_id = " . $db->Quote($user_id);

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
	 * @param	  string $email - email address to match accounts against
	 * @return	 array Return - array of auth link entries for the given user_id
	 */
	public static function find_by_email($email = null, $exclude = array())
	{
		if (empty($email))
		{
			return false;
		}

		$db = \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		$sql  = "SELECT l.*, d.authenticator as auth_domain_name FROM #__auth_link as l, #__auth_domain as d";
		$sql .= " WHERE l.auth_domain_id = d.id AND l.email = " . $db->Quote($email);

		if (!empty($exclude[0]))
		{
			foreach ($exclude as $e)
			{
				$sql .= " AND l.auth_domain_id != " . $db->Quote($e);
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
	 * Short description for 'delete_by_user_id'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $uid Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	public static function delete_by_user_id($uid = null)
	{
		if (empty($uid))
			return true;

		$db = \JFactory::getDBO();

		if (empty($db))
		{
				return false;
		}

		$db->setQuery("DELETE FROM #__auth_link WHERE user_id= " . $db->Quote($uid) . ";");

		if (!$db->query())
		{
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'find_or_create'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $type Parameter description (if any) ...
	 * @param	  unknown $authenticator Parameter description (if any) ...
	 * @param	  unknown $domain Parameter description (if any) ...
	 * @param	  unknown $username Parameter description (if any) ...
	 * @return	 mixed Return description (if any) ...
	 */
	public static function find_or_create($type,$authenticator,$domain,$username)
	{
		$hzad = Domain::find_or_create($type,$authenticator,$domain);

		if (!is_object($hzad))
			return false;

		if (empty($username))
			return false;

		$hzal = new self();
		$hzal->username = $username;
		$hzal->auth_domain_id = $hzad->id;

		$hzal->read();

		if (empty($hzal->id) && !$hzal->create())
			return false;

		return $hzal;
	}

	/**
	 * Short description for 'find_trusted_emails'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $user_id Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	public function find_trusted_emails( $user_id )
	{
		if (empty($user_id))
			return false;

		if (!is_numeric($user_id))
			return false;

		$db = \JFactory::getDBO();

		if (empty($db))
			return false;

		$sql = "SELECT email FROM #__auth_link WHERE user_id = " . $db->Quote($user_id) . ";";

		$db->setQuery($sql);

		$result = $db->loadResultArray();

		if (empty($result))
		{
			return false;
		}

		return $result;
	}
}

