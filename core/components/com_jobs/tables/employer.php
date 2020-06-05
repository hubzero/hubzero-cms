<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job employer
 */
class Employer extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_employers', 'id', $db);
	}

	/**
	 * Check if a user is an employer
	 *
	 * @param   string   $uid
	 * @param   integer  $admin
	 * @return  boolean
	 */
	public function isEmployer($uid, $admin=0)
	{
		if ($uid === null)
		{
			return false;
		}

		$now = \Date::toSql();
		$query  = "SELECT e.id FROM `$this->_tbl` AS e  ";
		if (!$admin)
		{
			$query .= "JOIN `#__users_points_subscriptions` AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE e.uid = " . $this->_db->quote($uid) . " AND s.status=1";
			$query .= " AND s.expires > " . $this->_db->quote($now) . " ";
		}
		else
		{
			$query .= "WHERE e.uid = 1";
		}
		$this->_db->setQuery($query);
		if ($this->_db->loadResult())
		{
			return true;
		}
		return false;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $uid  User ID
	 * @return  boolean  True upon success
	 */
	public function loadEmployer($uid=null)
	{
		if ($uid === null)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM `$this->_tbl` WHERE uid=" . $this->_db->quote($uid));
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}

	/**
	 * Get an employer
	 *
	 * @param   integer $ uid               User ID
	 * @param   string   $subscriptionCode  Subscription code
	 * @return  mixed    False if errors, Array upon success
	 */
	public function getEmployer($uid = null, $subscriptionCode = null)
	{
		if ($uid === null or $subscriptionCode === null)
		{
			return false;
		}
		$query  = "SELECT e.* ";
		$query .= "FROM `$this->_tbl` AS e ";
		if ($subscriptionCode == 'admin')
		{
			$query .= "WHERE e.uid = 1";
		}
		else if ($subscriptionCode)
		{
			$query .= "JOIN `#__users_points_subscriptions` AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE s.code=" . $this->_db->quote($subscriptionCode);
		}
		else if ($uid)
		{
			$query .= "WHERE e.uid = " . $this->_db->quote($uid);
		}
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result)
		{
			return $result[0];
		}
		return false;
	}
}
