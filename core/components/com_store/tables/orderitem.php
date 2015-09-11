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
 * Table class for store order item
 */
class OrderItem extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__order_items', 'id', $db);
	}

	/**
	 * Get all items for an order
	 *
	 * @param   integer  $oid  Order ID
	 * @return  mixed
	 */
	public function getOrderItems($oid)
	{
		if ($oid == null)
		{
			return false;
		}
		$sql = "SELECT r.*, s.*
				FROM $this->_tbl AS r
				LEFT JOIN #__store AS s ON s.id=r.itemid
				WHERE r.oid=" . $oid;
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Count all items for an order
	 *
	 * @param   integer  $oid  Order ID
	 * @return  mixed
	 */
	public function countAllItemOrders($oid)
	{
		if ($oid == null)
		{
			return false;
		}
		$sql = "SELECT count(*)
				FROM $this->_tbl AS r, #__store AS s
				WHERE s.id=r.itemid
				AND r.oid=" . $oid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Count all active items for an order
	 *
	 * @param   integer  $oid  Order ID
	 * @return  mixed
	 */
	public function countActiveItemOrders($oid)
	{
		if ($oid == null)
		{
			return false;
		}
		$sql = "SELECT count(*)
				FROM $this->_tbl AS r, #__store AS s, #__orders AS o
				WHERE o.status=0
				AND s.id=r.itemid
				AND o.id=r.oid
				AND r.itemid=" . $oid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}

