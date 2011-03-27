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


class Hubzero_Favorite extends JTable
{
	var $id    = NULL;  // int(11) Primary key
	var $uid   = NULL;  // int(11)
	var $oid   = NULL;  // int(11)
	var $tbl   = NULL;  // int(11)
	var $faved = NULL;  // datetime(0000-00-00 00:00:00)

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__xfavorites', 'id', $db );
	}
	
	//-----------
	
	public function loadFavorite( $uid=NULL, $oid=NULL, $tbl=NULL ) 
	{
		if ($uid === NULL) {
			return false;
		}
		if ($oid === NULL) {
			return false;
		}
		if ($tbl === NULL) {
			return false;
		}
		
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE uid='$uid' AND oid='$oid' AND tbl='$tbl' LIMIT 1" );
		$this->id = $this->_db->loadResult();
		
		return $this->load( $this->id );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('Missing user ID') );
			return false;
		}
		if (trim( $this->oid ) == '') {
			$this->setError( JText::_('Missing object ID') );
			return false;
		}
		if (trim( $this->tbl ) == '') {
			$this->setError( JText::_('Missing object table') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function buildQuery($filters) 
	{
		$filter = '';
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query = "SELECT t.*";
		} else {
			$query = "SELECT count(*)";
		}
		$query .= " FROM $this->_tbl AS t";
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " ORDER BY t.faved ASC LIMIT ".$filters['start'].",".$filters['limit'];
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

