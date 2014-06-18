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
 * Table class for store order item
 */
class OrderItem extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $oid    	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $uid    	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $itemid     = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $price    	= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $quantity   = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $selections = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__order_items', 'id', $db);
	}

	/**
	 * Get all items for an order
	 *
	 * @param      integer $oid Order ID
	 * @return     mixed
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
	 * @param      integer $oid Order ID
	 * @return     mixed
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
	 * @param      integer $oid Order ID
	 * @return     mixed
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

