<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

class SefEntry extends JTable 
{
	var $id	     = null;  // @var int
	var $cpt     = null;  // @var int
	var $oldurl	 = null;  // @var string
	var $newurl	 = null;  // @var string
	var $dateadd = null;  // @var date
	
	//-----------
	
	public function __construct( &$_db ) 
	{
		parent::__construct( '#__redirection', 'id', $_db );
	}
	
	//-----------
	
	public function getCount( $filters=array(), $admin=false ) 
	{
		$sql  = "SELECT COUNT(*) ";
		$sql .= $this->buildQuery( $filters, $admin );

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array(), $admin=false ) 
	{
		$sql  = "SELECT * ";
		$sql .= $this->buildQuery( $filters, $admin );
		$sql .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function buildQuery( $filters=array(), $admin=false )
	{
		$query = "FROM $this->_tbl WHERE ";
		if ($filters['ViewModeId'] == 1) {
			$query .= "`dateadd` > '0000-00-00' AND `newurl` = '' ";
		} elseif ( $filters['ViewModeId'] == 2 ) {
			$query .= "`dateadd` > '0000-00-00' AND `newurl` != '' ";
		} else {
			$query .= "`dateadd` = '0000-00-00'";
		}
		$query .= " ORDER BY ";
		switch ($filters['SortById'])
		{
			case 1 :
				$query .= "`oldurl` DESC";
				break;
			case 2 :
				$query .= "`newurl`";
				break;
			case 3 :
				$query .= "`newurl` DESC";
				break;
			case 4 :
				$query .= "`cpt`";
				break;
			case 5 :
				$query .= "`cpt` DESC";
				break;
			default :
				$query .= "`oldurl`";
				break;
		}
		return $query;
	}
}
