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
// XMessage database classes
//----------------------------------------------------------

class XMessageHelper extends JObject 
{
	public function takeAction( $type, $uids=array(), $component='', $element=null ) 
	{
		// Do we have the proper bits?
		if (!$element || !$component || !$type) {
			return false;
		}
		
		// Do we have any user IDs?
		if (count($uids) > 0) {
			$database =& JFactory::getDBO();
			
			// Loop through each ID
			foreach ($uids as $uid) 
			{
				// Find any actions the user needs to take for this $component and $element
				$action = new XMessageAction( $database );
				$mids = $action->getActionItems( $component, $element, $uid, $type );
				
				// Check if the user has any action items
				if (count($mids) > 0) {
					$recipient = new XMessageRecipient( $database );
					if (!$recipient->setState( 1, $mids )) {
						$this->setError( JText::sprintf('Unable to update recipient records %s for user %s', implode(',',$mids), $uid) );
					}
				}
			}
		}
		
		return true;
	}
	
	//-----------
	
	public function sendMessage( $type, $subject, $message, $from=array(), $to=array(), $component='', $element=null, $description='', $group_id=0 ) 
	{
		// Do we have a message?
		if (!$message) {
			return false;
		}
		
		// Do we have a subject line? If not, create it from the message
		if (!$subject && $message) {
			$subject = substr($message, 0, 70);
			if (strlen($subject) >= 70) {
				$subject .= '...';
			}
		}
		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		// Create the message object and store it in the database
		$xmessage = new XMessage( $database );
		$xmessage->subject    = $subject;
		$xmessage->message    = $message;
		$xmessage->created    = date( 'Y-m-d H:i:s', time() );
		$xmessage->created_by = $juser->get('id');
		$xmessage->component  = $component;
		$xmessage->type       = $type;
		$xmessage->group_id   = $group_id;
		if (!$xmessage->store()) {
			return $xmessage->getError();
		}
		
		// Does this message require an action?
		$action = new XMessageAction( $database );
		if ($element || $description) {
			$action->class   = $component;
			$action->element = $element;
			$action->description = $description;
			if (!$action->store()) {
				return $action->getError();
			}
		}
		
		// Do we have any recipients?
		if (count($to) > 0) {
			// Loop through each recipient
			foreach ($to as $uid) 
			{
				// Create a recipient object that ties a user to a message
				$recipient = new XMessageRecipient( $database );
				$recipient->uid = $uid;
				$recipient->mid = $xmessage->id;
				$recipient->created = date( 'Y-m-d H:i:s', time() );
				$recipient->expires = date( 'Y-m-d H:i:s', time() + (168 * 24 * 60 * 60) );
				$recipient->actionid = $action->id;
				if (!$recipient->store()) {
					return $recipient->getError();
				}
				
				// Get the user's methods for being notified
				$notify = new XMessageNotify( $database );
				$methods = $notify->getRecords( $uid, $type );

				$user =& JUser::getInstance($uid);

				// Load plugins
				JPluginHelper::importPlugin( 'xmessage' );
				$dispatcher =& JDispatcher::getInstance();
				
				// Do we have any methods?
				if ($methods) {
					// Loop through each method
					foreach ($methods as $method) 
					{
						$action = strtolower($method->method);

						if (!$dispatcher->trigger( 'onMessage', array($from, $xmessage, $user, $action) )) {
							$this->setError( JText::sprintf('Unable to message user %s with method %s', $uid, $action) );
						}
					}
				/*} else {
					$records = $notify->getRecords( $uid, 'all' );
					
					if (!$records) {
						// Default to email in the case the user has no methods
						if (!$dispatcher->trigger( 'onMessage', array($from, $xmessage, $user, 'email') ) {
							$this->setError( JText::sprintf('Unable to message user %s with method %s', $uid, $action) );
						}
					}*/
				}
			}
		}
		
		return true;
	}
}

//----------------------------------------------------------
// XMessage database classes
//----------------------------------------------------------

class XMessage extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $created    = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by = NULL;  // @var int(11)
	var $message    = NULL;  // @var text
	var $subject    = NULL;  // @var varchar(150)
	var $component  = NULL;  // @var varchar(100)
	var $type       = NULL;  // @var varchar(100)
	var $group_id   = NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->message ) == '') {
			$this->setError( JText::_('Please provide a message.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$query = "SELECT * FROM $this->_tbl ORDER BY created DESC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	private function buildQuery( $filters=array() ) 
	{
		if (isset($filters['group_id']) && $filters['group_id'] != 0) {
			$query  = "FROM $this->_tbl AS m, 
						#__users AS u  
						WHERE m.created_by=u.id ";
		} else {
			$query  = "FROM $this->_tbl AS m, 
						#__xmessage_recipient AS r,
						#__users AS u  
						WHERE r.uid=u.id 
						AND r.mid=m.id ";
		}
		if (isset($filters['created_by']) && $filters['created_by'] != 0) {
			$query .= " AND m.created_by=".$filters['created_by'];
		}
		if (isset($filters['group_id']) && $filters['group_id'] != 0) {
			$query .= " AND m.group_id=".$filters['group_id'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " ORDER BY created DESC";
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		return $query;
	}
	
	//-----------
	
	public function getSentMessages( $filters=array() ) 
	{
		if (isset($filters['group_id']) && $filters['group_id'] != 0) {
			$query = "SELECT m.*, u.name ".$this->buildQuery( $filters );
		} else {
			$query = "SELECT m.*, r.uid, u.name ".$this->buildQuery( $filters );
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getSentMessagesCount( $filters=array() ) 
	{
		$filters['limit'] = 0;
		
		$query = "SELECT COUNT(*) ".$this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}


class XMessageNotify extends JTable
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


class XMessageRecipient extends JTable
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


class XMessageAction extends JTable
{
	var $id          = NULL;  // @var int(11) Primary key
	var $class       = NULL;  // @var varchar(20)
	var $element     = NULL;  // @var int(11)
	var $description = NULL;  // @var text
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_action', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->element ) == '') {
			$this->setError( JText::_('Please provide an element.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getActionItems( $type=null, $component=null, $element=null, $uid=null ) 
	{
		if (!$uid) {
			return false;
		}
		if (!$type) {
			return false;
		}
		if (!$component) {
			$component = $this->class;
		}
		if (!$component) {
			return false;
		}
		if (!$element) {
			$element = $this->element;
		}
		if (!$element) {
			return false;
		}
		
		$query = "SELECT m.id 
				FROM #__xmessage_recipient AS r, $this->_tbl AS a, #__xmessage AS m
				WHERE m.id=r.mid AND r.actionid = a.id AND m.type='$type' AND r.uid='$uid' AND a.class='$component' AND a.element='$element'";

		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
}


class XMessageSeen extends JTable
{
	var $mid      = NULL;  // @var int(11)
	var $uid      = NULL;  // @var int(11)
	var $whenseen = NULL;  // @var datetime(0000-00-00 00:00:00)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_seen', 'uid', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->mid ) == '') {
			$this->setError( JText::_('Please provide a message ID.') );
			return false;
		}
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('Please provide a user ID.') );
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

	public function store( $new=false ) 
	{
		if (!$new) {
			$this->_db->setQuery( "UPDATE $this->_tbl SET whenseen='$this->whenseen' WHERE mid='$this->mid' AND uid='$this->uid'");
			if ($this->_db->query()) {
				$ret = true;
			} else {
				$ret = false;
			}
		} else {
			//$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
			$this->_db->setQuery( "INSERT INTO $this->_tbl (mid, uid, whenseen) VALUES ('$this->mid', '$this->uid', '$this->whenseen')");
			if ($this->_db->query()) {
				$ret = true;
			} else {
				$ret = false;
			}
		}
		if (!$ret) {
			$this->setError( strtolower(get_class( $this )).'::store failed <br />' . $this->_db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}
}


class XMessageComponent extends JTable
{
	var $id        = NULL;  // @var int(11) Primary key
	var $component = NULL;  // @var varchar(50)
	var $action    = NULL;  // @var varchar(100)
	var $title     = NULL;  // @var varchar(255)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_component', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->component ) == '') {
			$this->setError( JText::_('Please provide a component.') );
			return false;
		}
		if (trim( $this->action ) == '') {
			$this->setError( JText::_('Please provide an action.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getRecords() 
	{
		$query  = "SELECT x.*, c.name 
					FROM $this->_tbl AS x, #__components AS c
					WHERE x.component=c.option AND c.parent=0
					ORDER BY x.component, x.action DESC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getComponents() 
	{
		$query  = "SELECT DISTINCT x.component 
					FROM $this->_tbl AS x
					ORDER BY x.component ASC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
}
?>