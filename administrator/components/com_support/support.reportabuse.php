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
// Answers question database class
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
	
	function __construct( &$db )
	{
		parent::__construct( '#__abuse_reports', 'id', $db );
	}
	
	function check() 
	{
		if (trim( $this->report ) == '' && trim( $this->subject ) == JText::_('OTHER')) {
			$this->setError( JText::_('Please describe the issue.') );
			return false;
		}
		return true;
	}
	
	function buildQuery( $filters=array() ) 
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
	
	function getCount( $filters=array() ) 
	{
		//$query = "SELECT COUNT(*) FROM $this->_tbl AS a WHERE a.referenceid='".$filters['id']."' AND a.category='".$filters['category']."' AND a.state=0";
		$filters['sortby'] = '';
		
		$query  = "SELECT COUNT(*)";
		$query .= $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	function getRecords( $filters=array() ) 
	{
		$query  = "SELECT *";
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

?>