<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Market History class:
// Logs batch transactions, royalty distributions and other big transactions
//----------------------------------------------------------

class Hubzero_Bank_MarketHistory extends JTable 
{
	var $id          	= NULL;  // @var int(11) Primary key
	var $itemid      	= NULL;  // @var int(11)
	var $category    	= NULL;  // @var varchar(50)
	var $market_value	= NULL;  // @var decimal(11,2)
	var $date      		= NULL;  // @var datetime
	var $action	 		= NULL;  // @var varchar(50)
	var $log    		= NULL;  // @var text
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__market_history', 'id', $db );
	}
	
	//-----------
	
	public function getRecord($itemid=0, $action='', $category='', $created='', $log = '') 
	{
		if ($itemid === NULL) {
			$itemid = $this->itemid;
		}
		if ($action === NULL) {
			$action = $this->action;
		}
		if ($category === NULL) {
			$category = $this->category;
		}
			
		$sql = "SELECT id FROM #__market_history WHERE ";
		if ($itemid) {
			$sql.= " itemid='".$itemid."'";
		} else {
			$sql.= " 1=1";
		}
		if ($action) {
			$sql.= " AND action='".$action."'";
		}
		if ($category) {
			$sql.= " AND category='".$category."'";
		}
		if ($created) {
			$sql.= " AND date LIKE '".$created."%'";
		}
		if ($log) {
			$sql.= " AND log='".$log."'";
		}
		
		$sql.= " LIMIT 1";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
}

