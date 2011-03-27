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
// Report Abuse database class
//----------------------------------------------------------

class ReportAbuse extends JTable 
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $report   		= NULL;  // @var text
	var $created    	= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by 	= NULL;  // @var int(11)
	var $state      	= NULL;  // @var int(3)
	var $referenceid    = NULL;  // @var int(11)
	var $category		= NULL;  // @var varchar(50)
	var $subject		= NULL;  // @var varchar(150)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__abuse_reports', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->report ) == '' && trim( $this->subject ) == JText::_('OTHER')) {
			$this->setError( JText::_('Please describe the issue.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function buildQuery( $filters=array() ) 
	{
		$query = " FROM $this->_tbl AS a WHERE";
					
		if (isset($filters['state']) && $filters['state'] == 1) {
			$query .= " a.state=1";
		} else {
			$query .= " a.state=0";
		}
		if (isset($filters['id']) && $filters['id'] != '') {
			$query .= " AND a.referenceid='".$filters['id']."'";
		}
		if (isset($filters['category']) && $filters['category'] != '') {
			$query .= " AND a.category='".$filters['category']."'";
		}
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY ".$filters['sortby']." LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		return $query;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$filters['sortby'] = '';
		
		$query  = "SELECT COUNT(*)";
		$query .= $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$query  = "SELECT *";
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

