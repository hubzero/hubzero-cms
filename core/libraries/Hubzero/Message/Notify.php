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

namespace Hubzero\Message;

/**
 * Table class for message notification
 */
class Notify extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xmessage_notify', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->uid = intval($this->uid);
		if (!$this->uid)
		{
			$this->setError(\Lang::txt('Please provide a user ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Get records for a user
	 *
	 * @param   integer  $uid   User ID
	 * @param   string   $type  Record type
	 * @return  mixed    False if errors, array on success
	 */
	public function getRecords($uid=null, $type=null)
	{
		$uid  = $uid  ?: $this->uid;
		$type = $type ?: $this->type;

		if (!$uid)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE `uid`=" . $this->_db->quote($uid);
		$query .= ($type) ? " AND `type`=" . $this->_db->quote($type) : "";
		$query .= " ORDER BY `priority` ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Clear all entries for a user
	 *
	 * @param   integer  $uid  User ID
	 * @return  boolean  True on success
	 */
	public function clearAll($uid=null)
	{
		$uid = $uid ?: $this->uid;

		if (!$uid)
		{
			return false;
		}

		$query  = "DELETE FROM $this->_tbl WHERE `uid`=" . $this->_db->quote($uid);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}

	/**
	 * Delete notifications for action
	 * 
	 * @param   string   $type
	 * @return  boolean  True on success, False on error
	 */
	public function deleteType($type)
	{
		if (!$type)
		{
			return false;
		}

		$query  = "DELETE FROM $this->_tbl WHERE `type`=" . $this->_db->quote($type);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}
}

