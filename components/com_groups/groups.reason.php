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
// Extended database class
//----------------------------------------------------------

class GroupsReason extends JTable 
{
	var $id       = NULL;  // @var int(11) Primary key
	var $uidNumber = NULL;  // @var int(11)
	var $gidNumber      = NULL;  // @var int(11)
	var $reason   = NULL;  // @var text
	var $date     = NULL;  // @var datetime
	
	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__xgroups_reasons', 'id', $db );
	}
	
	//-----------
	
	public function loadReason( $uid, $gid ) 
	{
		if ($uid === NULL || $gid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uidNumber='$uid' AND gidNumber='$gid' LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------

	public function deleteReason( $uid, $gid ) 
	{
		if ($uid === NULL || $gid === NULL) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE uidNumber='$uid' AND gidNumber='$gid'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
		return true;
	}

	//-----------
	
	public function check() 
	{
		if (trim( $this->gidNumber ) == '') {
			$this->setError( JText::_('GROUPS_REASON_MUST_HAVE_GROUPID') );
			return false;
		}
		
		if (trim( $this->uidNumber ) == '') {
			$this->setError( JText::_('GROUPS_REASON_MUST_HAVE_USERNAME') );
			return false;
		}
		
		return true;
	}
}
?>