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


class Wishlist extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $category       = NULL;  // @var varchar(50)
	var $referenceid	= NULL;  // @var int(11)
	var $description	= NULL;  // @var text
	var $title			= NULL;  // @var varchar(150)
	var $created    	= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by 	= NULL;  // @var int(11)
	var $state     		= NULL;  // @var int(3)
	var $public			= NULL;  // @var int(3)  // can any user view and submit to it?
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wishlist', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('Missing title for the wish list') );
			return false;
		}

		return true;
	}
	
	//------------
	
	public function get_wishlistID($rid=0, $cat='resource')
	{
		if ($rid === NULL) {
			$rid = $this->referenceid;
		}
		if ($rid === NULL) {
			return false;
		}
		
		// get individuals
		$sql = "SELECT id"
			. "\n FROM $this->_tbl "
			. "\n WHERE referenceid='".$rid."' AND category='".$cat."' ORDER BY id DESC LIMIT 1";

		$this->_db->setQuery( $sql );
		return  $this->_db->loadResult();
	}
	
	//------------
	
	public function createlist($category='resource', $refid, $public=1, $title='', $description='')
	{
		if ($refid === NULL) {
			return false;
		}
		
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$juser =& JFactory::getUser();
				
		$this->created = date( 'Y-m-d H:i:s' );
		$this->category = $category;
		$this->created_by = $juser->get('id');
		$this->referenceid = $refid;
		$this->description = $description;
		$this->public = $public;
	
		switch ($category) 
		{
			case 'general':
				$this->title = $title ? $title : $hubShortName;
					
				if (!$this->store()) {
					$this->_error = $this->getError();
					return false;
				} else {
					// Checkin wishlist
					$this->checkin();
				}
			
				return $this->id;			
			break;
			
			case 'resource':
				// resources can only have one list
				if (!$this->get_wishlist('',$refid, 'resource')) {	
					$this->title = $title ? $title :'Resource #'.$rid;
					
					if (!$this->store()) {
						$this->_error = $this->getError();
						return false;
					} else {
						// Checkin wishlist
						$this->checkin();
					}
			
					return $this->id;
				} else {
					return $this->get_wishlistID($refid); // return existing id
				}
			break;
			
			case 'group':
				$this->title = $title ? $title :'Group #'.$rid;
				if (!$this->store()) {
					$this->_error = $this->getError();
					return false;
				} else {
					// Checkin wishlist
					$this->checkin();
				}
			
				return $this->id;
			break;
			
			case 'user':
				$this->title = $title;
				if (!$this->store()) {
					$this->_error = $this->getError();
					return false;
				} else {
					// Checkin wishlist
					$this->checkin();
				}
			
				return $this->id;
			break;
		} 
				
		return 0;
	}
	
	//------------
	
	public function getTitle($id)
	{
		if ($id === NULL) {
			return false;
		}
		$sql = "SELECT w.title "
				. "\n FROM $this->_tbl AS w";
		$sql .=	"\n WHERE w.id=".$id;
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
	
	//------------
	
	public function is_primary($id)
	{
		if ($id === NULL) {
			return false;
		}
		$sql = "SELECT w.* FROM $this->_tbl AS w WHERE w.id=".$id." AND w.referenceid=1 AND w.category='general'";
		
		$this->_db->setQuery( $sql );
		$bingo = $this->_db->loadResult();
		if ($bingo) {
			return true;
		} else {
			return false;
		}
	}
	
	//------------
	
	public function get_wishlist($id='', $refid=0, $cat='', $primary = 0, $getversions=0)
	{
		if ($id===NULL && $refid===0 && $cat===NULL) {
			return false;
		}
		if ($id && !intval($id)) {
			return false;
		}
		if ($refid && !intval($refid)) {
			return false;
		}
		
		$sql = "SELECT w.*";
		//if($cat == 'resource') {
			//$sql .= "\n , r.title as resourcetitle, r.type as resourcetype, r.alias, r.introtext";
		//}
			$sql .= "\n FROM $this->_tbl AS w";
		//if($cat == 'resource') {
			//$sql .= "\n JOIN #__resources AS r ON r.id=w.referenceid";	
		//}
		if ($id) {
			$sql .=	"\n WHERE w.id=".$id;
		} else if ($refid && $cat) {
			$sql .=	"\n WHERE w.referenceid=".$refid." AND w.category='".$cat."'";
		} else if ($primary) {
			$sql .=	"\n WHERE w.referenceid=1 AND w.category='general'";
		}
			
		$this->_db->setQuery( $sql );
		$res = $this->_db->loadObjectList();
		$wishlist = ($res) ? $res[0] : array();
		
		// get parent 
		//$parent = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);
		
		if (count($wishlist) > 0 && $wishlist->category=='resource') {
			$wishlist->resource = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);
			// Currenty for tools only
			if ($getversions && $wishlist->resource && isset($wishlist->resource->type) && $wishlist->resource->type==7) {
				$wishlist->resource->versions = $this->get_parent_versions($wishlist->referenceid, $wishlist->resource->type );
			}
		}
		
		return $wishlist;
	}
	//-----------

	public function get_parent_versions($rid, $type)
	{
		$versions = array();
		// currently for tools only
		if ($type == 7) {
			$query = "SELECT v.id FROM #__tool_version as v JOIN #__resources as r ON r.alias = v.toolname WHERE r.id='".$rid."'";
			$query.= " AND v.state=3 ";
			$query.= " OR v.state!=3 ORDER BY state DESC, revision DESC LIMIT 3";
			$this->_db->setQuery( $query );
			$result  = $this->_db->loadObjectList();
			$versions = $result ? $result : array();
		}
		
		return $versions;
	}
	
	//-----------

	public function get_wishlist_parent($refid, $cat='resource')
	{
		$resource = array();
		if ($cat == 'resource') {
			$sql = "SELECT r.title, r.type, r.alias, r.introtext, t.type as typetitle"
				. "\n FROM #__resources AS r"
				. "\n LEFT JOIN #__resource_types AS t ON t.id=r.type "
				. "\n WHERE r.id='".$refid."'";
			$this->_db->setQuery( $sql );
			$res  = $this->_db->loadObjectList();
			$resource = ($res) ? $res[0]: array();
		}
		
		return $resource;
	}
	
	//---------
	
	public function getCons($refid) 
	{
		$sql = "SELECT n.uidNumber AS id"
			 . "\n FROM #__xprofiles AS n"
			 . "\n JOIN #__author_assoc AS a ON n.uidNumber=a.authorid"
			 . "\n WHERE a.subtable = 'resources'"
			 . "\n AND a.subid=". $refid;
	
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getToolDevGroup($refid, $groups = array())
	{
		$query  = "SELECT g.cn FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= " JOIN #__tool AS t ON g.toolid=t.id ";
		$query .= " JOIN #__resources as r ON r.alias = t.toolname";
		$query .= " WHERE r.id = '".$refid."' AND g.role=1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}	
}
