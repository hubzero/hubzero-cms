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


class Hubzero_Message_Notify extends JTable
{
	var $id       = NULL;  // @var int(11) Primary key
	var $uid      = NULL;  // @var int(11)
	var $method   = NULL;  // @var text
	var $type     = NULL;  // @var text
	var $priority = NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_notify', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('Please provide a user ID.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getRecords( $uid=null, $type=null ) 
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}
		if (!$type) {
			$type = $this->type;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE `uid`='$uid'";
		$query .= ($type) ? " AND `type`='$type'" : "";
		$query .= " ORDER BY `priority` ASC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function clearAll( $uid=null ) 
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}
		
		$query  = "DELETE FROM $this->_tbl WHERE `uid`='$uid'";
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			return false;
		}
	}
}
