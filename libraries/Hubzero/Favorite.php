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
