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
// Blog Comment database class
//----------------------------------------------------------

class KbComment extends JTable 
{
	var $id         = NULL;  // @var int(11) primary key
	var $entry_id   = NULL;  // @var int(11)
	var $content    = NULL;  // @var text
	var $created    = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $created_by = NULL;  // @var int(11)
	var $anonymous  = NULL;  // @var int(3)
	var $parent     = NULL;  // @var int(11)
	var $helpful    = NULL;  // @var int(11)
	var $nothelpful = NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__faq_comments', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->content ) == '') {
			$this->setError( JText::_('Your comment must contain text.') );
			return false;
		}
		if (!$this->entry_id) {
			$this->setError( JText::_('Missing entry ID.') );
			return false;
		}
		if (!$this->created_by) {
			$this->setError( JText::_('Missing creator ID.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadUserComment( $entry_id, $user_id ) 
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE entry_id=".$entry_id." AND created_by=".$user_id." LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function getComments( $entry_id=NULL, $parent=NULL )
	{
		if (!$entry_id) {
			$entry_id = $this->entry_id;
		}
		if (!$parent) {
			$parent = 0;
		}
		
		$juser =& JFactory::getUser();
		
		//$sql = "SELECT * FROM $this->_tbl WHERE entry_id=$entry_id AND parent=$parent ORDER BY created ASC";
		if (!$juser->get('guest')) {
			$sql  = "SELECT c.*, v.vote FROM $this->_tbl AS c ";
			$sql .= "LEFT JOIN #__faq_helpful_log AS v ON v.object_id=c.id AND v.user_id=".$juser->get('id')." AND v.type='comment' ";
		} else {
			$sql = "SELECT c.* FROM $this->_tbl AS c ";
		}
		$sql .= "WHERE c.entry_id=$entry_id AND c.parent=$parent ORDER BY created ASC";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getAllComments( $entry_id=NULL )
	{
		if (!$entry_id) {
			$entry_id = $this->entry_id;
		}
		
		$comments = $this->getComments( $entry_id, 0 );
		if ($comments) {
			$ra = null;
			if (is_file(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'reportabuse.php')) {
				include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'reportabuse.php' );
				$ra = new ReportAbuse( $this->_db );
			}
			foreach ($comments as $key => $row) 
			{
				if ($ra) {
					$comments[$key]->reports = $ra->getCount( array('id'=>$comments[$key]->id, 'category'=>'kb') );
				}
				$comments[$key]->replies = $this->getComments( $entry_id, $row->id );
				if ($comments[$key]->replies) {
					foreach ($comments[$key]->replies as $ky => $rw) 
					{
						if ($ra) {
							$comments[$key]->replies[$ky]->reports = $ra->getCount( array('id'=>$rw->id, 'category'=>'kb') );
						}
						$comments[$key]->replies[$ky]->replies = $this->getComments( $entry_id, $rw->id );
						if ($comments[$key]->replies[$ky]->replies && $ra) {
							foreach ($comments[$key]->replies[$ky]->replies as $kyy => $rwy) 
							{
								$comments[$key]->replies[$ky]->replies[$kyy]->reports = $ra->getCount( array('id'=>$rwy->id, 'category'=>'kb') );
							}
						}
					}
				}
			}
		}
		return $comments;
	}
	
	//-----------
	
	public function deleteChildren( $id=NULL )
	{
		if (!$id) {
			$id = $this->id;
		}
		
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE parent=".$id );
		$comments = $this->_db->loadObjectList();
		if ($comments) {
			foreach ($comments as $row) 
			{
				// Delete abuse reports
				/*$this->_db->setQuery( "DELETE FROM #__abuse_reports WHERE referenceid=".$row->id." AND category='blog'" );
				if (!$this->_db->query()) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}*/
				// Delete children
				$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE parent=".$row->id );
				if (!$this->_db->query()) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
				}
			}
			$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE parent=".$id );
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}
		return true;
	}
}
