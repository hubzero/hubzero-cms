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

namespace Components\Support\Tables;

use Lang;

/**
 * Table class for watching a ticket
 */
class Watching extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct($db)
	{
		parent::__construct('#__support_watching', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string   $oid  Record alias
	 * @return  boolean  True on success
	 */
	public function load($oid=null, $user_id=null)
	{
		if ($oid === null)
		{
			return false;
		}
		if ($user_id === null)
		{
			return parent::load($oid);
		}

		$query = "SELECT * FROM $this->_tbl WHERE ticket_id=" . $this->_db->quote(trim($oid)) . " AND user_id=" . $this->_db->quote(intval($user_id));

		$this->_db->setQuery($query);
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
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->ticket_id = intval($this->ticket_id);
		if (!$this->ticket_id)
		{
			$this->setError(Lang::txt('A ticket ID must be provided.'));
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(Lang::txt('A user ID must be provided.'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	public function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS w";

		$where = array();

		if (isset($filters['ticket_id']) && $filters['ticket_id'] > 0)
		{
			$where[] = "w.ticket_id=" . $this->_db->quote($filters['ticket_id']);
		}
		if (isset($filters['user_id']) && $filters['user_id'] > 0)
		{
			$where[] = "w.user_id=" . $this->_db->quote($filters['user_id']);
		}
		if (isset($filters['open']))
		{
			$query .= " INNER JOIN `#__support_tickets` AS t ON t.id=w.ticket_id";

			$where[] = "t.open=" . $this->_db->quote($filters['open']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['order']) && $filters['order'] != '')
		{
			$query .= " ORDER BY " . $filters['order'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*)" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT w.*" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

