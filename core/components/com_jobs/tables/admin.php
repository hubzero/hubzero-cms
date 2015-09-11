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
 * Table class for job admins
 */
class JobAdmin extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_admins', 'id', $db);
	}

	/**
	 * Check if a user is an admin for a job
	 *
	 * @param      integer $uid User ID
	 * @param      integer $jid Job ID
	 * @return     boolean True if admin
	 */
	public function isAdmin($uid,  $jid)
	{
		if ($uid === NULL or $jid === NULL)
		{
			return false;
		}

		$query  = "SELECT id ";
		$query .= "FROM #__jobs_admins  ";
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
	 * @param      integer $jid Job ID
	 * @return     array
	 */
	public function getAdmins($jid)
	{
		if ($jid === NULL)
		{
			return false;
		}

		$admins = array();

		$query  = "SELECT uid ";
		$query .= "FROM #__jobs_admins ";
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

