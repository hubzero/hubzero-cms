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

class WikiAuthor extends JTable 
{
	var $id  = NULL;  // @var int(11) Primary key
	var $pid = NULL;  // @var int(11)
	var $uid = NULL;  // @var int(11)
	
	//-----------
	
	function __construct( &$db ) 
	{
		parent::__construct( '#__wiki_authors', 'id', $db );
	}
	
	//-----------
	
	function getID( $pid=NULL, $uid=NULL ) 
	{
		if ($pid == NULL) {
			$pid = $this->pid;
		}
		if ($uid == NULL) {
			$uid = $this->uid;
		}
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE pid='". $pid ."' AND uid='".$uid."'" );
		$this->id = $this->_db->loadResult();
	}
	
	//-----------
	
	function getAuthors( $pid=NULL )
	{
		if ($pid == NULL) {
			$pid = $this->pid;
		}
		$this->_db->setQuery( "SELECT uid FROM $this->_tbl WHERE pid='". $pid ."'" );
		$authors = $this->_db->loadObjectList();
		
		$auths = array();
		if (count($authors) > 0) {
			foreach ($authors as $auth) 
			{
				$auths[] = $auth->uid;
			}
		}
		return $auths;
	}
	
	//-----------
	
	function deleteAuthors( $pid=NULL )
	{
		if ($pid == NULL) {
			$pid = $this->pid;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE pid='".$pid."'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
		return true;
	}
	
	//-----------

	function check() 
	{
		if ($this->pid == '') {
			$this->setError( 'Author entry must have a page ID.' );
			return false;
		}
		if ($this->uid == '') {
			$this->setError( 'Author entry must have a user ID.' );
			return false;
		}
		return true;
	}
}

?>