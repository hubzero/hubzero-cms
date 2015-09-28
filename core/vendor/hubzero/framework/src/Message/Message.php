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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * @param   object  &$db  Database
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
			$this->setError(\Lang::txt('Please provide a message.'));
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
			$where[] = "m.created_by=" . $this->_db->quote($filters['created_by']);
		}
		if (isset($filters['daily_limit']) && $filters['daily_limit'] != 0)
		{
			$start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . " 00:00:00";
			$end   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . " 23:59:59";

			$where[] = "m.created >= " . $this->_db->quote($start);
			$where[] = "m.created <= " . $this->_db->quote($end);
		}
		if (isset($filters['group_id']) && $filters['group_id'] != 0)
		{
			$where[] = "m.group_id=" . $this->_db->quote($filters['group_id']);
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

