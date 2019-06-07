<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job application
 */
class JobApplication extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_applications', 'id', $db);
	}

	/**
	 * Get job applications
	 *
	 * @param   integer  $jobid  Job ID
	 * @return  mixed    False if errors, Array upon success
	 */
	public function getApplications($jobid)
	{
		if ($jobid === null)
		{
			return false;
		}

		$sql  = "SELECT a.* FROM `$this->_tbl` AS a ";
		$sql .= "JOIN `#__jobs_seekers` as s ON s.uid=a.uid";
		$sql .= " WHERE  a.jid=" . $this->_db->quote($jobid) . " AND s.active=1 ";
		$sql .= " ORDER BY a.applied DESC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $uid      User ID
	 * @param   integer  $jid      Job ID
	 * @param   string   $jobcode  Job code
	 * @return  boolean  True upon success
	 */
	public function loadApplication($uid = null, $jid = null, $jobcode = null)
	{
		if ($uid === null or ($jid === null && $jobcode === null))
		{
			return false;
		}

		$query  = "SELECT A.* FROM `$this->_tbl` as A ";
		$query .= $jid ? "" : " JOIN `#__jobs_openings` as J ON J.id=A.jid ";
		$query .= " WHERE A.uid=" . $this->_db->quote($uid) . " ";
		$query .=  $jid ? "AND A.jid=" . $this->_db->quote($jid) . " " : "AND J.code=" . $this->_db->quote($jobcode) . " ";
		$query .= " LIMIT 1";
		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}
}
