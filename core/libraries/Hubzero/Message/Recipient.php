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
 * Table class for recipient of message
 */
class Recipient extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xmessage_recipient', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->mid = intval($this->mid);
		if (!$this->mid)
		{
			$this->setError(\Lang::txt('Please provide a message ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load a record by message ID and user ID and bind to $this
	 *
	 * @param   integer  $mid  Message ID
	 * @param   integer  $uid  User ID
	 * @return  boolean  True on success
	 */
	public function loadRecord($mid=NULL, $uid=NULL)
	{
		$mid = $mid ?: $this->mid;
		$uid = $uid ?: $this->uid;

		if (!$mid || !$uid)
		{
			return false;
		}

		return parent::load(array(
			'mid' => $mid,
			'uid' => $uid
		));
	}

	/**
	 * Builds a query string based on filters passed
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function buildQuery($uid, $filters=array())
	{
		$query  = "FROM #__xmessage AS m LEFT JOIN #__xmessage_seen AS s ON s.mid=m.id AND s.uid=" . $this->_db->quote($uid) . ", $this->_tbl AS r
					WHERE r.uid=" . $this->_db->quote($uid) . "
					AND r.mid=m.id ";
		if (isset($filters['state']))
		{
			$query .= "AND r.state=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['filter']) && $filters['filter'] != '')
		{
			$query .= "AND m.component=" . $this->_db->quote($filters['filter']);
		}
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " ORDER BY importance DESC, created DESC";
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}
		return $query;
	}

	/**
	 * Get records for a user based on filters passed
	 *
	 * @param   integer  $uid      User ID
	 * @param   array    $filters  Filters to build query from
	 * @return  mixed    False if errors, array on success
	 */
	public function getMessages($uid=null, $filters=array())
	{
		$uid = $uid ?: $this->uid;

		if (!$uid)
		{
			return false;
		}

		$query = "SELECT m.*, s.whenseen, r.expires, r.actionid, r.state,
					(CASE WHEN r.actionid > 0 AND s.whenseen IS NULL THEN 1 ELSE 0 END) AS importance " . $this->buildQuery($uid, $filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a record count for a user based on filters passed
	 *
	 * @param   integer  $uid      User ID
	 * @param   array    $filters  Filters to build query from
	 * @return  mixed    False if errors, integer on success
	 */
	public function getMessagesCount($uid=null, $filters=array())
	{
		$uid = $uid ?: $this->uid;

		if (!$uid)
		{
			return false;
		}

		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->buildQuery($uid, $filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of unread messages for a user
	 *
	 * @param   integer  $uid    User ID
	 * @param   integer  $limit  Number of records to return
	 * @return  mixed    False if errors, array on success
	 */
	public function getUnreadMessages($uid=null, $limit=null)
	{
		$uid = $uid ?: $this->uid;

		if (!$uid)
		{
			return false;
		}

		$query = "SELECT " . ($limit ? "DISTINCT m.*, r.expires, r.actionid" : "DISTINCT m.id") . "
				FROM #__xmessage AS m, $this->_tbl AS r
				WHERE m.id = r.mid AND r.uid=" . $this->_db->quote($uid) . " AND r.state!=2 AND m.id NOT IN (SELECT s.mid FROM #__xmessage_seen AS s WHERE s.uid=" . $this->_db->quote($uid) . ")";
		if ($limit)
		{
			$query .= " ORDER BY r.created DESC";
			$query .= " LIMIT $limit";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete all messages marked as trash for a user
	 *
	 * @param   integer  $uid  User ID
	 * @return  boolean  True on success
	 */
	public function deleteTrash($uid=null)
	{
		$uid = $uid ?: $this->uid;

		if (!$uid)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE uid=" . $this->_db->quote($uid) . " AND state='2'";

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getError());
			return false;
		}
		return true;
	}

	/**
	 * Set the state of multiple messages
	 *
	 * @param   integer  $state  State to set
	 * @param   array    $ids    List of message IDs
	 * @return  boolean  True on success
	 */
	public function setState($state=0, $ids=array())
	{
		if (count($ids) <= 0)
		{
			return false;
		}

		$ids = array_map('intval', $ids);
		$ids = implode(',', $ids);
		$query = "UPDATE $this->_tbl SET state=" . $this->_db->quote($state) . " WHERE id IN ($ids)";

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getError());
			return false;
		}
		return true;
	}
}

