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


class WishRank extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $wishid      	= NULL;  // @var int
	var $userid 		= NULL;  // @var int
	var $voted    	    = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $importance     = NULL;  // @var int(3)
	var $effort		    = NULL;  // @var int(3)
	var $due    	    = NULL;  // @var datetime (0000-00-00 00:00:00)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wishlist_vote', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->wishid ) == '') {
			$this->setError( JText::_('WISHLIST_ERROR_NO_WISHID') );
			return false;
		}

		return true;
	}
	
	//--------------
	
	public function load_vote( $oid=NULL, $wishid=NULL ) 
	{
		if ($oid === NULL) {
			$oid = $this->userid;
		}
		if ($wishid === NULL) {
			$wishid = $this->wishid;
		}
		
		if ($oid === NULL or $wishid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM #__wishlist_vote WHERE userid='$oid' AND wishid='$wishid'");
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//--------------
	
	public function get_votes( $wishid=NULL ) 
	{
		if ($wishid === NULL) {
			$wishid = $this->wishid;
		}
		
		if ($wishid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM #__wishlist_vote WHERE wishid='$wishid'");
		return $this->_db->loadObjectList();
	}
	
	//--------------
	
	public function remove_vote( $wishid=NULL, $oid=NULL ) 
	{
		if ($oid === NULL) {
			$oid = $this->userid;
		}
		if ($wishid === NULL) {
			$wishid = $this->wishid;
		}
		
		if ($wishid === NULL) {
			return false;
		}
		
		$query = "DELETE FROM #__wishlist_vote WHERE wishid='$wishid'";
		if ($oid) {
			$query .= " AND userid=".$oid;
		}
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		return true;
	}
}
