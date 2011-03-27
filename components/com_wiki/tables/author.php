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


class WikiAuthor extends JTable 
{
	var $id  = NULL;  // @var int(11) Primary key
	var $pid = NULL;  // @var int(11)
	var $uid = NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wiki_authors', 'id', $db );
	}
	
	//-----------
	
	public function getID( $pid=NULL, $uid=NULL ) 
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
	
	public function getAuthors( $pid=NULL )
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
	
	public function deleteAuthors( $pid=NULL )
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

	public function check() 
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

