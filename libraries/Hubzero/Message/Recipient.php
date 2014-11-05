<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @param   object  &$db  JDatabase
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
			$this->setError(\JText::_('Please provide a message ID.'));
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
	 * @param   array   $filters Filters to build query from
	 * @return  string  SQL
	 */
	private function buildQuery($uid, $filters=array())
	{
		$query  = "FROM #__xmessage AS m LEFT JOIN #__xmessage_seen AS s ON s.mid=m.id AND s.uid=" . $this->_db->Quote($uid) . ", $this->_tbl AS r
					WHERE r.uid=" . $this->_db->Quote($uid) . "
					AND r.mid=m.id ";
		if (isset($filters['state']))
		{
			$query .= "AND r.state=" . $this->_db->Quote($filters['state']);
		}
		if (isset($filters['filter']) && $filters['filter'] != '')
		{
			$query .= "AND m.component=" . $this->_db->Quote($filters['filter']);
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
				WHERE m.id = r.mid AND r.uid=" . $this->_db->Quote($uid) . " AND m.id NOT IN (SELECT s.mid FROM #__xmessage_seen AS s WHERE s.uid=" . $this->_db->Quote($uid) . ")";
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

		$query = "DELETE FROM $this->_tbl WHERE uid=" . $this->_db->Quote($uid) . " AND state='2'";

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
		$query = "UPDATE $this->_tbl SET state=" . $this->_db->Quote($state) . " WHERE id IN ($ids)";

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getError());
			return false;
		}
		return true;
	}
}

