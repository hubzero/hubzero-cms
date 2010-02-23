<?php
/**
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * @license	GNU General Public License, version 2 (GPLv2) 
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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class EventsPage extends JTable 
{
	var $id          = NULL;  // int(11)
	var $event_id    = NULL;  // int(11)
	var $alias       = NULL;  // string(100)
	var $title       = NULL;  // string(250)
	var $pagetext    = NULL;  // text
	var $created     = NULL;  // datetime(0000-00-00 00:00:00)
	var $created_by  = NULL;  // int(11)
	var $modified    = NULL;  // datetime(0000-00-00 00:00:00)
	var $modified_by = NULL;  // int(11)
	var $ordering    = NULL;  // int(11)
	var $params      = NULL;  // text

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__events_pages', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->alias ) == '') {
			$this->setError( JText::_('You must enter an alias.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadFromAlias( $alias=NULL, $event_id=NULL ) 
	{
		if ($alias === NULL) {
			return false;
		}
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE alias='$alias' AND event_id='$event_id'" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function loadFromEvent( $event_id=NULL ) 
	{
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE event_id='$event_id' ORDER BY ordering ASC LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function loadPages( $event_id=NULL ) 
	{
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT title, alias, id FROM $this->_tbl WHERE event_id='$event_id' ORDER BY ordering ASC" );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function deletePages( $event_id=NULL ) 
	{
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE event_id='$event_id'" );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getNeighbor( $move ) 
	{
		switch ($move) 
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE event_id=".$this->event_id." AND ordering < ".$this->ordering." ORDER BY ordering DESC LIMIT 1";
				break;
			
			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE event_id=".$this->event_id." AND ordering > ".$this->ordering." ORDER BY ordering LIMIT 1";
				break;
		}
		$this->_db->setQuery( $sql );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function buildQuery($filters) 
	{	
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query = "SELECT t.*, NULL as position";
		} else {
			$query = "SELECT count(*)";
		}
		$query .= " FROM $this->_tbl AS t";
		if (isset($filters['event_id']) && $filters['event_id'] != '') {
			$query .= " WHERE t.event_id='".$filters['event_id']."'";
		}
		if (isset($filters['search']) && $filters['search'] != '') {
			if (isset($filters['event_id']) && $filters['event_id'] != '') {
				$query .= " AND ";
			} else {
				$query .= " WHERE ";
			}
			$query .= "LOWER( t.title ) LIKE '%".$filters['search']."%'";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " ORDER BY t.ordering ASC LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		return $query;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$filters['limit'] = 0;
		
		$this->_db->setQuery( $this->buildQuery( $filters ) );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$this->_db->setQuery( $this->buildQuery( $filters ) );
		return $this->_db->loadObjectList();
	}
}
?>