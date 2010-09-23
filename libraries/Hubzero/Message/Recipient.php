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


class Hubzero_Message_Recipient extends JTable
{
	var $id       = NULL;  // @var int(11) Primary key
	var $mid      = NULL;  // @var int(11)
	var $uid      = NULL;  // @var int(11)
	var $created  = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $expires  = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $actionid = NULL;  // @var int(11)
	var $state    = NULL;  // @var tinyint(2)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_recipient', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->mid ) == '') {
			$this->setError( JText::_('Please provide a message ID.') );
			return false;
		}
		return true;
	}

	//-----------
	
	public function loadRecord( $mid=NULL, $uid=NULL ) 
	{
		if (!$mid) {
			$mid = $this->mid;
		}
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$mid || !$uid) {
			return false;
		}
		
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE mid='$mid' AND uid='$uid'" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	//-----------
	
	private function buildQuery( $uid, $filters=array() ) 
	{
		$query  = "FROM #__xmessage AS m LEFT JOIN #__xmessage_seen AS s ON s.mid=m.id AND s.uid='$uid', $this->_tbl AS r 
					WHERE r.uid='$uid' 
					AND r.mid=m.id ";
		if (isset($filters['state'])) {
			$query .= "AND r.state='".$filters['state']."'";
		}
		if (isset($filters['filter']) && $filters['filter'] != '') {
			$query .= "AND m.component='".$filters['filter']."'";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " ORDER BY importance DESC, created DESC";
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		return $query;
	}
	
	//-----------
	
	public function getMessages( $uid=null, $filters=array() ) 
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}
		
		$query = "SELECT m.*, s.whenseen, r.expires, r.actionid, r.state,
		 			(CASE WHEN r.actionid > 0 AND s.whenseen IS NULL THEN 1 ELSE 0 END) AS importance ".$this->buildQuery( $uid, $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getMessagesCount( $uid=null, $filters=array() ) 
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}
		
		$filters['limit'] = 0;
		
		$query = "SELECT COUNT(*) ".$this->buildQuery( $uid, $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getUnreadMessages( $uid=null, $limit=null ) 
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}
		
		$query = "SELECT DISTINCT m.*, r.expires, r.actionid 
				FROM #__xmessage AS m, $this->_tbl AS r
				WHERE m.id = r.mid AND r.uid='$uid' AND m.id NOT IN (SELECT s.mid FROM #__xmessage_seen AS s WHERE s.uid='$uid')";
		$query .= " ORDER BY created DESC";
		$query .= ($limit) ? " LIMIT $limit" : "";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function deleteTrash( $uid=null ) 
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}
		
		$query = "DELETE FROM $this->_tbl WHERE uid='$uid' AND state='2'";
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getError() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function setState( $state=0, $ids=array() ) 
	{
		if (count($ids) <= 0) {
			return false;
		}
		
		$ids = implode(',',$ids);
		$query = "UPDATE $this->_tbl SET state='$state' WHERE id IN ($ids)";
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getError() );
			return false;
		}
		return true;
	}
}
