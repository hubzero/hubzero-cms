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


class WikiPageComment extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $pageid     = NULL;  // @var int(11)
	var $version    = NULL;  // @var int(11)
	var $created    = NULL;  // @var datetime
	var $created_by = NULL;  // @var int(11)
	var $ctext      = NULL;  // @var text
	var $chtml      = NULL;  // @var text
	var $rating     = NULL;  // @var int(1)
	var $anonymous  = NULL;  // @var int(1)
	var $parent     = NULL;  // @var int(11)
	var $status     = NULL;  // @var int(1)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wiki_comments', 'id', $db );
	}
	
	//-----------
	
	public function getResponses() 
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE parent='$this->id'" );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function report( $oid=null ) 
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		$this->_db->setQuery( "UPDATE $this->_tbl SET status=1 WHERE $this->_tbl_key = '".$this->$k."'" );

		if ($this->_db->query()) {
			return true;
		} else {
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
	}
	
	//-----------
	
	public function getComments( $id, $parent, $ver='', $limit='' ) 
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE pageid='".$id."' AND parent=".$parent." $ver ORDER BY created DESC $limit" );
		return $this->_db->loadObjectList();
	}
}
