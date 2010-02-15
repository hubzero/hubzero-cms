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

//----------------------------------------------------------
// Shopping cart database class
//----------------------------------------------------------

class Cart extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $uid    	= NULL;  // @var int(11)
	var $itemid     = NULL;  // @var int(11)
	var $type    	= NULL;  // @var varchar(20)
	var $quantity   = NULL;  // @var int(11)
	var $added  	= NULL;  // @var datetime
	var $selections = NULL;  // @var text
	
	//------------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__cart', 'id', $db );
	}
	
	//------------
	
	public function checkCartItem( $id=null, $uid) 
	{
		if ($id == null or $uid == null) {
			return false;
		}
	
		$sql = "SELECT id, quantity FROM $this->_tbl WHERE itemid='".$id."' AND uid=".$uid;
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getCartItems($uid, $rtrn='')
	{
		$total = 0;
		if ($uid == null) {
			return false;
		}
		
		// clean-up items with zero quantity
		$sql = "DELETE FROM $this->_tbl WHERE quantity=0";
		$this->_db->setQuery($sql);
		$this->_db->query();
		
		$query  = "SELECT B.quantity, B.itemid, B.uid, B.added, B.selections, a.title, a.price, a.available, a.params, a.type, a.category ";		
		$query .= " FROM $this->_tbl AS B, #__store AS a";
		$query .= " WHERE a.id = B.itemid AND B.uid=".$uid;
		$query .= " ORDER BY B.id DESC";
		$this->_db->setQuery( $query);
		$result = $this->_db->loadObjectList();
		
		if ($result) {
			foreach ($result as $r) 
			{
				$price = $r->price * $r->quantity;
				if ($r->available) {
					$total = $total + $price;
				}
				
				$params 	 		=& new JParameter( $r->params );
				$selections  		=& new JParameter( $r->selections );
				
				// get size selection
				$r->sizes    		= $params->get( 'size', '' );
				$r->sizes 			= str_replace(" ","",$r->sizes);				
				$r->selectedsize    = trim($selections->get( 'size', '' ));
				$r->sizes    		= split(',',$r->sizes);
				
				// get color selection
				$r->colors    		= $params->get( 'color', '' );
				$r->colors 			= str_replace(" ","",$r->colors);				
				$r->selectedcolor   = trim($selections->get( 'color', '' ));
				$r->colors    		= split(',',$r->colors);
			}
		}
		
		if ($rtrn) {
			$result = $total; // total cost of items in cart
		}
		
		return $result;
	}

	//-----------
	
	public function saveCart( $posteditems, $uid)
	{		
		if ($uid == null) {
			return false;
		}
		
		// get current cart items
		$items = $this->getCartItems($uid);
		if ($items) {
			foreach ($items as $item) 
			{	
				if ($item->type!=2) { // not service	
					$size 			= (isset($item->selectedsize)) ? $item->selectedsize : '';
					$color 			= (isset($item->color)) ? $item->color : '';
					$sizechoice 	= (isset($posteditems['size'.$item->itemid])) ? $posteditems['size'.$item->itemid] : $size;
					$colorchoice 	= (isset($posteditems['color'.$item->itemid])) ? $posteditems['color'.$item->itemid] : $color;
					$newquantity 	= (isset($posteditems['num'.$item->itemid])) ? $posteditems['num'.$item->itemid] : $item->quantity;
							
					$selection	    = '';
					$selection	   .= 'size=';
					$selection 	   .= $sizechoice;
					$selection	   .= '\n';
					$selection	   .= 'color=';
					$selection 	   .= $colorchoice;
				
					$query  = "UPDATE $this->_tbl SET quantity='".$newquantity."',";
					$query .= " selections='".$selection."'";
					$query .= " WHERE itemid=".$item->itemid;
					$query .= " AND uid=".$uid;
					$this->_db->setQuery( $query);
					$this->_db->query();
				}
			}	
		}
	}
	
	//-----------
	
	public function deleteCartItem($id, $uid, $all=0) 
	{
		$sql = "DELETE FROM $this->_tbl WHERE uid='".$uid."'  ";
		if (!$all && $id) {
			$sql.= "AND itemid='".$id."' ";
		}

		$this->_db->setQuery( $sql);
		$this->_db->query();
	}
	
	//-----------
	
	public function deleteUnavail( $uid, $items)
	{		
		if ($uid == null) {
			return false;
		}
		if (count($items) > 0) {
			foreach ($items as $i) 
			{
				if ($i->available==0) {
					$sql = "DELETE FROM $this->_tbl WHERE itemid=".$i->itemid." AND uid=".$uid;
					$this->_db->setQuery( $sql);
					$this->_db->query();
				}	
			}
		}
	}
	
	//-----------
	
	public function deleteItem($itemid=null, $uid=null, $type='merchandise') 
	{
		if ($itemid == null) {
			return false;
		}
		if ($uid == null) {
			return false;
		}
		
		$sql = "DELETE FROM $this->_tbl WHERE itemid='$itemid' AND type='$type' AND uid=$uid";
		$this->_db->setQuery($sql);
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
}


class Store extends  JTable 
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $title    		= NULL; 
	var $price    		= NULL;  
	var $description    = NULL;  
	var $available    	= NULL;  
	var $published   	= NULL;
	var $featured   	= NULL;
	var $special   		= NULL;
	var $category   	= NULL; 
	var $type   		= NULL;  
	var $created  		= NULL;  // @var datetime
	var $params 		= NULL;  // @var text
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__store', 'id', $db );
	}
	
	//-----------
	
	public function getInfo( $id)
	{
		if ($id == null) {
		return false;
		}
		
		$query = "SELECT * FROM $this->_tbl WHERE id=".$id;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getItems( $rtrn='count', $filters, $config)
	{
		// build body of query
		$query  = "FROM $this->_tbl AS C WHERE ";
		
		if (isset($filters['filterby'])) {
			switch ($filters['filterby'])
			{
				case 'all': 
					$query .= "1=1";
					break;
				case 'available': 
					$query .= "C.available=1";
					break;
				case 'published': 
					$query .= "C.published=1";
					break;
				default: 
					$query .= "C.published=1";
					break;
			}
		} else {
			$query .= "C.published=1";
		}
	
		switch ($filters['sortby'])
		{
			case 'pricelow':   	$query .= " ORDER BY C.price DESC, C.publish_up ASC"; break;
			case 'pricehigh':   $query .= " ORDER BY C.price ASC, C.publish_up ASC"; break;
			case 'date':   		$query .= " ORDER BY C.created DESC"; break;
			case 'category':   	$query .= " ORDER BY C.category DESC"; break;
			case 'type':   		$query .= " ORDER BY C.type DESC"; break;
			default:       	  	$query .= " ORDER BY C.featured DESC, C.id DESC"; break; // featured and newest first
		}
		
		// build count query (select only ID)
		$query_count  = "SELECT count(*) ";
	
		// build fetch query
		$query_fetch  = "SELECT C.id, C.title, C.description, C.price, C.created, C.available, C.params, C.special, C.featured, C.category, C.type, C.published ";
		$query_fetch .= $query;
		if ($filters['limit'] && $filters['start']) {
			$query_fetch .= " LIMIT " . $start . ", " . $limit;
		}

		// execute query
		$result = NULL;
		if ($rtrn == 'count') {
			$this->_db->setQuery( $query_count );
			$result = $this->_db->loadResult();
		} else {
			$this->_db->setQuery( $query_fetch );
			$result = $this->_db->loadObjectList();
			if ($result) {
				for ($i=0; $i < count($result); $i++) 
				{
					$row = &$result[$i];
					//$row->created = StoreController::mkt($row->created);
					//$row->when = StoreController::timeAgo($row->created);
					
					$row->webpath 	= $config->get('webpath');
					$row->root 		= JPATH_ROOT;
					
					// Get parameters
					$params 		=& new JParameter( $row->params );
					$row->size    	= $params->get( 'size', '' );
					$row->color  	= $params->get( 'color', '' );
				}
			}
		}

		return $result;
	}
}


class Order extends JTable 
{
	var $id         		= NULL;  // @var int(11) Primary key
	var $uid    			= NULL;  // @var int(11)
	//var $type    			= NULL;  // @var varchar(20)
	var $total      		= NULL;  // @var int(11)
	var $status     		= NULL;  // @var int(11)
	var $details  			= NULL;  // @var text
	var $email    			= NULL;  // @var varchar(150)
	var $ordered  			= NULL;  // @var datetime
	var $status_changed  	= NULL;  // @var datetime
	var $notes  			= NULL;  // @var text
	
	//----------
	// order status:
	// 0 - 'placed (newly received)'
	// 1 - 'processed' (account debited)
	// 2 - 'cancelled'
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__orders', 'id', $db );
	}
	
	//-----------
	
	public function getOrderID( $uid, $ordered)
	{	
		if ($uid == null) {
			return false;
		}
		if ($ordered == null) {
			return false;
		}
		
		$sql = "SELECT id FROM $this->_tbl WHERE uid='".$uid."' AND ordered='".$ordered."' ";
		$this->_db->setQuery( $sql);
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getOrders( $rtrn='count', $filters)
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
		$query_count  = "SELECT count(*) FROM $this->_tbl AS m WHERE ".$where;
	
		$query_fetch = "SELECT m.id, m.uid, m.total, m.status, m.ordered, m.status_changed,  
				(SELECT count(*) FROM #__order_items AS r WHERE r.oid=m.id) AS items"
			. "\n FROM $this->_tbl AS m"
			. "\n WHERE ".$where
			. "\n ORDER BY ".$filters['sortby'];
		
		if ($filters['limit'] && $filters['start']) {
			$query_fetch .= " LIMIT " . $start . ", " . $limit;
		}

		// execute query
		$result = NULL;
		if ($rtrn == 'count') {
			$this->_db->setQuery( $query_count );
			$results = $this->_db->loadResult();
		} else {
			$this->_db->setQuery( $query_fetch );
			$result = $this->_db->loadObjectList();
		}

		return $result;
	}
}


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
		$sql = "SELECT r.*, s.*"
				. "\n FROM #__order_items AS r"
				. "\n LEFT JOIN #__store AS s ON s.id=r.itemid "
				. "\n WHERE r.oid=".$oid;
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
}
?>