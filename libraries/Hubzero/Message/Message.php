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


class Hubzero_Message_Message extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $created    = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by = NULL;  // @var int(11)
	var $message    = NULL;  // @var text
	var $subject    = NULL;  // @var varchar(150)
	var $component  = NULL;  // @var varchar(100)
	var $type       = NULL;  // @var varchar(100)
	var $group_id   = NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->message ) == '') {
			$this->setError( JText::_('Please provide a message.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$query = "SELECT * FROM $this->_tbl ORDER BY created DESC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	private function buildQuery( $filters=array() ) 
	{
		if (isset($filters['group_id']) && $filters['group_id'] != 0) {
			$query  = "FROM $this->_tbl AS m, 
						#__users AS u  
						WHERE m.created_by=u.id ";
		} else {
			$query  = "FROM $this->_tbl AS m, 
						#__xmessage_recipient AS r,
						#__users AS u  
						WHERE r.uid=u.id 
						AND r.mid=m.id ";
		}
		if (isset($filters['created_by']) && $filters['created_by'] != 0) {
			$query .= " AND m.created_by=".$filters['created_by'];
		}
		if (isset($filters['daily_limit']) && $filters['daily_limit'] != 0) {
			$start = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 00:00:00";
			$end = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 23:59:59";
			
			$query .= " AND m.created >= '$start' AND m.created <= '$end'";
		}
		if (isset($filters['group_id']) && $filters['group_id'] != 0) {
			$query .= " AND m.group_id=".$filters['group_id'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " ORDER BY created DESC";
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		return $query;
	}
	
	//-----------
	
	public function getSentMessages( $filters=array() ) 
	{
		if (isset($filters['group_id']) && $filters['group_id'] != 0) {
			$query = "SELECT m.*, u.name ".$this->buildQuery( $filters );
		} else {
			$query = "SELECT m.*, r.uid, u.name ".$this->buildQuery( $filters );
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getSentMessagesCount( $filters=array() ) 
	{
		$filters['limit'] = 0;
		
		$query = "SELECT COUNT(*) ".$this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

