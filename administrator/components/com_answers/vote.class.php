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

//----------------------------------------------------------
// Thumbs vote database class
//----------------------------------------------------------

class Vote extends JTable 
{
	var $id      		= NULL;  // @var int(11) Primary key
	var $referenceid    = NULL;  // @var int(11)
	var $voted 			= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $voter   		= NULL;  // @var int(11)
	var $helpful     	= NULL;  // @var varchar(11)
	var $ip      		= NULL;  // @var varchar(15)
	var $category     	= NULL;  // @var varchar(50)
	
	function __construct( &$db )
	{
		parent::__construct( '#__vote_log', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->referenceid ) == '') {
			$this->setError( JText::_('Missing reference ID') );
			return false;
		}
		return true;
		
		if (trim( $this->category ) == '') {
			$this->setError( JText::_('Missing category') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	function checkVote($refid=null, $category=null, $voter=null) 
	{
		if ($refid == null) {
			$refid = $this->referenceid;
		}
		if ($refid == null) {
			return false;
		}
		if ($category == null) {
			$category = $this->category;
		}
		if ($category == null) {
			return false;
		}
		
		$now = date( 'Y-m-d H:i:s', time() );
		
		$query = "SELECT count(*) FROM $this->_tbl WHERE referenceid='".$refid."' AND category = '".$category."' AND voter='".$voter."'";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getResults( $filters=array() ) 
	{
		$query = "SELECT c.* 
				FROM $this->_tbl AS c 
				WHERE c.referenceid=".$filters['id']." AND category='".$filters['category']."' ORDER BY c.voted DESC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

?>
