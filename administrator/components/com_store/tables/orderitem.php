<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class OrderItem extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $oid    	= NULL;  // @var int(11)
	var $uid    	= NULL;  // @var int(11)
	var $itemid     = NULL;  // @var int(11)
	var $price    	= NULL;  // @var int(11)
	var $quantity   = NULL;  // @var int(11)
	var $selections = NULL;  // @var text
	
	//----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__order_items', 'id', $db );
	}
	
	//----------
	
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
	
	//----------
	
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
	
	//----------
	
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
