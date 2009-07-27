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
// Resource Review class
//----------------------------------------------------------

class ResourcesReview extends JTable 
{
	var $resource_id = NULL;  // @var int(11)
	var $user_id     = NULL;  // @var int(11)
	var $rating      = NULL;  // @var decimal(2,1)
	var $comment     = NULL;  // @var text
	var $created     = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $anonymous   = NULL;  // @var int(3)
	var $id          = NULL;  // @var int(11) primary key
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_ratings', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->rating ) == '') {
			$this->setError( JText::_('Your review must have a rating.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadUserReview( $resourceid, $userid ) 
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE resource_id=".$resourceid." AND user_id=".$userid." LIMIT 1" );
		
		//return $this->_db->loadObject( $this );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function loadUserRating( $resourceid, $userid ) 
	{
		$this->_db->setQuery( "SELECT rating FROM $this->_tbl WHERE resource_id=".$resourceid." AND user_id=".$userid." LIMIT 1" );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRatings( $id=NULL )
	{
		$juser =& JFactory::getUser();
		
		if (!$id) {
			$id = $this->resource_id;
		}
		//$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE resource_id=".$id." ORDER BY created DESC" );
		$this->_db->setQuery( "SELECT rr.*, rr.id as id, v.helpful AS vote, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=rr.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=rr.id) AS nothelpful "
			."\n FROM $this->_tbl AS rr "
			."\n LEFT JOIN #__vote_log AS v ON v.referenceid=rr.id AND v.category='review' AND v.voter='".$juser->get('id')."' "
			."\n WHERE rr.resource_id=".$id."  ORDER BY rr.created DESC" );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getRating( $id=NULL, $userid )
	{
		if(!$userid) {
		$juser =& JFactory::getUser();
		$userid = $juser->get('id');
		}
		
		if (!$id) {
			$id = $this->resource_id;
		}
		//$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE resource_id=".$id." ORDER BY created DESC" );
		$this->_db->setQuery( "SELECT rr.*, rr.id as id, v.helpful AS vote, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=rr.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=rr.id) AS nothelpful "
			."\n FROM $this->_tbl AS rr "
			."\n LEFT JOIN #__vote_log AS v ON v.referenceid=rr.id AND v.category='review' AND v.voter='".$userid."' "
			."\n WHERE rr.id=".$id." " );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getVote( $id, $category = 'review', $uid )
	{
		if (!$id) {
			$id = $this->id;
		}
		
		if ($id === NULL or $uid === NULL) {
			return false;
		}
		
		
		$query  = "SELECT v.helpful ";
		$query .= "FROM #__vote_log as v  ";
		$query .= "WHERE v.referenceid = '".$id."' AND v.category='".$category."' AND v.voter='".$uid."' LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
		
	}
	
}
?>