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

namespace Components\Store\Tables;

/**
 * Table class for store order
 */
class Order extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__orders', 'id', $db);
	}

	/**
	 * Get the ID of an order for a user from a certain date
	 *
	 * @param   integer  $uid      User ID
	 * @param   string   $ordered  Timestamp
	 * @return  mixed
	 */
	public function getOrderID($uid, $ordered)
	{
		if ($uid == null)
		{
			return false;
		}
		if ($ordered == null)
		{
			return false;
		}

		$sql = "SELECT id FROM $this->_tbl WHERE uid=" . $this->_db->quote($uid) . " AND ordered=" . $this->_db->quote($ordered);
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get all orders
	 *
	 * @param   string  $rtrn     Data return type (record count or array of records)
	 * @param   array   $filters  Filters to build query from
	 * @return  mixed
	 */
	public function getOrders($rtrn='count', $filters)
	{
		switch ($filters['filterby'])
		{
			case 'all':
				$where = "1=1";
			break;
			case 'new':
				$where = "m.status=0";
			break;
			case 'processed':
				$where = "m.status=1";
			break;
			case 'cancelled':
			default:
				$where = "m.status=2";
			break;
		}

		// build count query (select only ID)
		$query_count  = "SELECT count(*) FROM $this->_tbl AS m WHERE " . $where;

		$query_fetch = "SELECT m.id, m.uid, m.total, m.status, m.ordered, m.status_changed,
				(SELECT count(*) FROM #__order_items AS r WHERE r.oid=m.id) AS items"
			. "\n FROM $this->_tbl AS m"
			. "\n WHERE " . $where
			. "\n ORDER BY " . $filters['sortby'];

		if ($filters['limit'] && $filters['start'])
		{
			$query_fetch .= " LIMIT " . $start . ", " . $limit;
		}

		// execute query
		$result = NULL;
		if ($rtrn == 'count')
		{
			$this->_db->setQuery($query_count);
			$results = $this->_db->loadResult();
		}
		else
		{
			$this->_db->setQuery($query_fetch);
			$result = $this->_db->loadObjectList();
		}

		return $result;
	}
}

