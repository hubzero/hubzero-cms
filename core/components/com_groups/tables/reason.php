<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for group membership reason
 */
class Reason extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_reasons', 'id', $db);
	}

	/**
	 * Load a record based on group ID and user ID and bind to $this
	 *
	 * @param   integer  $uid  User ID
	 * @param   integer  $gid  Group ID
	 * @return  boolean  True on success
	 */
	public function loadReason($uid, $gid)
	{
		if ($uid === null || $gid === null)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uidNumber=" . $this->_db->quote($uid) . " AND gidNumber=" . $this->_db->quote($gid) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Delete an entry based on group ID and user ID
	 *
	 * @param   integer  $uid  User ID
	 * @param   integer  $gid  Group ID
	 * @return  boolean  True on success
	 */
	public function deleteReason($uid, $gid)
	{
		if ($uid === null || $gid === null)
		{
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE uidNumber=" . $this->_db->quote($uid) . " AND gidNumber=" . $this->_db->quote($gid));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
		}
		return true;
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->gidNumber) == '')
		{
			$this->setError(Lang::txt('GROUPS_REASON_MUST_HAVE_GROUPID'));
			return false;
		}

		if (trim($this->uidNumber) == '')
		{
			$this->setError(Lang::txt('GROUPS_REASON_MUST_HAVE_USERNAME'));
			return false;
		}

		return true;
	}
}
