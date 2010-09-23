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
