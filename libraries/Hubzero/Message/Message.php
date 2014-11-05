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
 * Table class for a message
 */
class Message extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xmessage', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->message = trim($this->message);
		if (!$this->message)
		{
			$this->setError(\JText::_('Please provide a message.'));
			return false;
		}

		$this->group_id   = intval($this->group_id);
		$this->created_by = intval($this->created_by);

		return true;
	}

	/**
	 * Get a record count based on filters passed
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records based on filters passed
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		$query = "SELECT * FROM $this->_tbl ORDER BY created DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Builds a query string based on filters passed
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function buildQuery($filters=array())
	{
		if (isset($filters['group_id']) && $filters['group_id'] != 0)
		{
			$query  = "FROM $this->_tbl AS m
						JOIN #__users AS u ON u.id=m.created_by";
		}
		else
		{
			$query  = "FROM $this->_tbl AS m
						JOIN #__xmessage_recipient AS r ON r.mid=m.id
						JOIN #__users AS u ON u.id=r.uid";
		}

		$where = array();

		if (isset($filters['created_by']) && $filters['created_by'] != 0)
		{
			$where[] = "m.created_by=" . $this->_db->Quote($filters['created_by']);
		}
		if (isset($filters['daily_limit']) && $filters['daily_limit'] != 0)
		{
			$start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . " 00:00:00";
			$end   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . " 23:59:59";

			$where[] = "m.created >= " . $this->_db->Quote($start);
			$where[] = "m.created <= " . $this->_db->Quote($end);
		}
		if (isset($filters['group_id']) && $filters['group_id'] != 0)
		{
			$where[] = "m.group_id=" . $this->_db->Quote($filters['group_id']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get sent messages
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getSentMessages($filters=array())
	{
		if (isset($filters['group_id']) && $filters['group_id'] != 0)
		{
			$query = "SELECT m.*, u.name ";
		}
		else
		{
			$query = "SELECT m.*, u.name, r.uid ";
		}

		$query .= $this->buildQuery($filters);

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " ORDER BY created DESC";
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a record count of messages sent
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getSentMessagesCount($filters=array())
	{
		$query = "SELECT COUNT(*) " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

