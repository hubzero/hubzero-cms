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
