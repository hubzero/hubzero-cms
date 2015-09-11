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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$sql .= " WHERE  a.jid=" . $this->_db->quote($jobid) . " AND s.active=1 ";
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

