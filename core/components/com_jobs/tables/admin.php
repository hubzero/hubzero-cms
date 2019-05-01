<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job admins
 */
class JobAdmin extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_admins', 'id', $db);
	}

	/**
	 * Check if a user is an admin for a job
	 *
	 * @param   integer  $uid  User ID
	 * @param   integer  $jid  Job ID
	 * @return  boolean  True if admin
	 */
	public function isAdmin($uid,  $jid)
	{
		if ($uid === null or $jid === null)
		{
			return false;
		}

		$query  = "SELECT id ";
		$query .= "FROM `$this->_tbl` ";
		$query .= "WHERE uid = " . $this->_db->quote($uid) . " AND jid = " . $this->_db->quote($jid);
		$this->_db->setQuery($query);
		if ($this->_db->loadResult())
		{
			return true;
		}
		return false;
	}

	/**
	 * Get a list of administrators
	 *
	 * @param   integer  $jid  Job ID
	 * @return  array
	 */
	public function getAdmins($jid)
	{
		if ($jid === null)
		{
			return false;
		}

		$admins = array();

		$query  = "SELECT uid ";
		$query .= "FROM `$this->_tbl` ";
		$query .= "WHERE jid = " . $this->_db->quote($jid);
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result)
		{
			foreach ($result as $r)
			{
				$admins[] = $r->uid;
			}
		}

		return $admins;
	}
}
