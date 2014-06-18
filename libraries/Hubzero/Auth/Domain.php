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
 * Short description for 'Hubzero\Auth\Domain'
 *
 * Long description (if any) ...
 */
class Domain
{

	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	private $id;

	/**
	 * Description for 'type'
	 *
	 * @var unknown
	 */
	private $type;

	/**
	 * Description for 'authenticator'
	 *
	 * @var unknown
	 */
	private $authenticator;

	/**
	 * Description for 'domain'
	 *
	 * @var unknown
	 */
	private $domain;

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
	 * @return     void
	 */
	private function __construct()
	{
	}

	/**
	 * Short description for 'clear'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
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
	 * @param      unknown $msg Parameter description (if any) ...
	 * @return     void
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
	 * @param      unknown $type Parameter description (if any) ...
	 * @param      unknown $authenticator Parameter description (if any) ...
	 * @param      unknown $domain Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
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
	 * Short description for 'createInstance'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $type Parameter description (if any) ...
	 * @param      unknown $authenticator Parameter description (if any) ...
	 * @param      unknown $domain Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function find_by_id($id)
	{
		$hzad = new self();
		$hzad->id = $id;

		$hzad->read();

		if (empty($hzad->authenticator))
			return false;

		return $hzad;
	}

	public function createInstance($type,$authenticator,$domain = null)
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
	 * Short description for 'create'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
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
			$query = "INSERT INTO #__auth_domain (id,type,authenticator,domain,params) VALUES ( " . $db->Quote($this->id) .
				"," . $db->Quote($this->type) . "," . $db->Quote($this->authenticator) . "," . $db->Quote($this->domain) . ","
				. $db->Quote($this->params) . ");";

			$db->setQuery();

			$result = $db->query();

			if ($result !== false || $db->getErrorNum() == 1062)
			{
				return true;
			}
		}
		else
		{
			$query = "INSERT INTO #__auth_domain (type,authenticator,domain,params) VALUES ( " .
				$db->Quote($this->type) . "," . $db->Quote($this->authenticator) . "," . $db->Quote($this->domain) . "," . $db->Quote($this->params) . ");";

			$db->setQuery($query);

			$result = $db->query();
			//var_dump($db);
			if ($result === false && $db->getErrorNum() == 1062)
			{
				$query = "SELECT id FROM #__auth_domain WHERE authenticator=" .
					$db->Quote($this->authenticator) . " AND domain=" . $db->Quote($this->domain) . " AND type=" . $db->Quote($this->type) . ";";

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

		if (empty($db))
		{
			return false;
		}

		if (is_numeric($this->id))
		{
			$query = "SELECT id,type,authenticator,domain,params FROM #__auth_domain WHERE id=" .
				$db->Quote($this->id) . ";";
		}
		else
		{
			$query = "SELECT id,type,authenticator,domain,params FROM #__auth_domain WHERE "
				. " type=" . $db->Quote($this->type)
				. " AND authenticator=" . $db->Quote($this->authenticator)
				. " AND domain=" . $db->Quote($this->domain)
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
	 * Short description for 'update'
	 *
	 * Long description (if any) ...
	 *
	 * @param      boolean $all Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function update($all = false)
	{
		$db =  \JFactory::getDBO();

		$query = "UPDATE #__auth_domain SET ";

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
	 * @param      boolean $deletelinks Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function delete($deletelinks = false)
	{
		if (!isset($this->toolname) && !isset($this->id))
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
			$db->setQuery("SELECT id FROM #__auth_domain WHERE authenticator=" .
					$db->Quote($this->authenticator) . " AND domain=" . $db->Quote($this->domain) . ";");

			$this->id = $db->loadResult();
		}

		if (empty($this->id))
		{
			return false;
		}

		$db->setQuery("DELETE FROM #__auth_domain WHERE id= " . $db->Quote($this->id) . ";");

		if (!$db->query())
		{
			return false;
		}

		if ($deletelinks)
		{
			$db->setQuery("UPDATE #__auth_links SET auth_domain_id=NULL WHERE auth_domain_id=" .
				$db->Quote($this->id) . ";");

			$db->query();
		}

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

		$this->$property = $value;

		if (!in_array($property, $this->_updatedkeys))
			$this->_updatedkeys[] = $property;
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
	 * @param      string $property Parameter description (if any) ...
	 * @return     void
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
	 * Short description for 'find_or_create'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $type Parameter description (if any) ...
	 * @param      unknown $authenticator Parameter description (if any) ...
	 * @param      unknown $domain Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function find_or_create($type,$authenticator,$domain=null)
	{
		$hzad = new self();
		$hzad->type = $type;
		$hzad->authenticator = $authenticator;
		$hzad->domain = $domain;
		$hzad->read();

		if (empty($hzad->id) && !$hzad->create())
			return false;

		return $hzad;
	}
}

