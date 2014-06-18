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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for store order
 */
class Order extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $uid    			= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $total      		= NULL;

	/**
	 * int(11)
	 * 0 - 'placed (newly received)'
	 * 1 - 'processed' (account debited)
	 * 2 - 'cancelled'
	 * @var unknown
	 */
	var $status     		= NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $details  			= NULL;

	/**
	 * varchar(150)
	 *
	 * @var string
	 */
	var $email    			= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $ordered  			= NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $status_changed  	= NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $notes  			= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__orders', 'id', $db);
	}

	/**
	 * Get the ID of an order for a user from a certain date
	 *
	 * @param      integer $uid     User ID
	 * @param      string  $ordered Timestamp
	 * @return     mixed
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

		$sql = "SELECT id FROM $this->_tbl WHERE uid='" . $uid . "' AND ordered='" . $ordered . "' ";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get all orders
	 *
	 * @param      string  $rtrn    Data return type (record count or array of records)
	 * @param      array   $filters Filters to build query from
	 * @return     mixed
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

