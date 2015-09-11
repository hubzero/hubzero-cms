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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tables;

/**
 * Table class for group membership reason
 */
class Reason extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_reasons', 'id', $db);
	}

	/**
	 * Load a record based on group ID and user ID and bind to $this
	 *
	 * @param   integer $uid User ID
	 * @param   integer $gid Group ID
	 * @return  boolean True on success
	 */
	public function loadReason($uid, $gid)
	{
		if ($uid === NULL || $gid === NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uidNumber='$uid' AND gidNumber='$gid' LIMIT 1");
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
		if ($uid === NULL || $gid === NULL)
		{
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE uidNumber='$uid' AND gidNumber='$gid'");
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

