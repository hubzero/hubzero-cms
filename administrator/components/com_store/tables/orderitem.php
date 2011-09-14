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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'OrderItem'
 * 
 * Long description (if any) ...
 */
class OrderItem extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'oid'
	 * 
	 * @var unknown
	 */
	var $oid    	= NULL;  // @var int(11)


	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid    	= NULL;  // @var int(11)


	/**
	 * Description for 'itemid'
	 * 
	 * @var unknown
	 */
	var $itemid     = NULL;  // @var int(11)


	/**
	 * Description for 'price'
	 * 
	 * @var unknown
	 */
	var $price    	= NULL;  // @var int(11)


	/**
	 * Description for 'quantity'
	 * 
	 * @var unknown
	 */
	var $quantity   = NULL;  // @var int(11)


	/**
	 * Description for 'selections'
	 * 
	 * @var unknown
	 */
	var $selections = NULL;  // @var text

	//----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__order_items', 'id', $db );
	}

	/**
	 * Short description for 'getOrderItems'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $oid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getOrderItems($oid)
	{
		if ($oid == null) {
			return false;
		}
		$sql = "SELECT r.*, s.* 
				FROM $this->_tbl AS r 
				LEFT JOIN #__store AS s ON s.id=r.itemid 
				WHERE r.oid=".$oid;
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'countAllItemOrders'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $oid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function countAllItemOrders($oid)
	{
		if ($oid == null) {
			return false;
		}
		$sql = "SELECT count(*) 
				FROM $this->_tbl AS r, #__store AS s 
				WHERE s.id=r.itemid 
				AND r.oid=".$oid;
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'countActiveItemOrders'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $oid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function countActiveItemOrders($oid)
	{
		if ($oid == null) {
			return false;
		}
		$sql = "SELECT count(*) 
				FROM $this->_tbl AS r, #__store AS s, #__orders AS o 
				WHERE o.status=0 
				AND s.id=r.itemid 
				AND o.id=r.oid 
				AND r.itemid=".$oid;
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
}

