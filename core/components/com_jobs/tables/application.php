<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Jobs\Tables;

/**
 * Table class for job application
 */
class JobApplication extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_applications', 'id', $db);
	}

	/**
	 * Get job applications
	 *
	 * @param      integer $jobid Job ID
	 * @return     mixed False if errors, Array upon success
	 */
	public function getApplications($jobid)
	{
		if ($jobid === NULL)
		{
			return false;
		}

		$sql  = "SELECT a.* FROM  #__jobs_applications AS a ";
		$sql .= "JOIN #__jobs_seekers as s ON s.uid=a.uid";
		$sql .= " WHERE  a.jid=" . $this->_db->Quote($jobid) . " AND s.active=1 ";
		$sql .= " ORDER BY a.applied DESC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $uid     User ID
	 * @param      integer $jid     Job ID
	 * @param      string  $jobcode Job code
	 * @return     boolean True upon success
	 */
	public function loadApplication($uid = NULL, $jid = NULL, $jobcode = NULL)
	{
		if ($uid === NULL or ($jid === NULL && $jobcode === NULL))
		{
			return false;
		}

		$query  = "SELECT A.* FROM $this->_tbl as A ";
		$query .= $jid ? "" : " JOIN #__jobs_openings as J ON J.id=A.jid ";
		$query .= " WHERE A.uid=" . $this->_db->Quote($uid) . " ";
		$query .=  $jid ? "AND A.jid=" . $this->_db->Quote($jid) . " " : "AND J.code=" . $this->_db->Quote($jobcode) . " ";
		$query .= " LIMIT 1";
		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}
}

