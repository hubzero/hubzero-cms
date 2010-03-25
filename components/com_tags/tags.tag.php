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
// Tag Database class
//----------------------------------------------------------

class TagsTag extends JTable
{
	var $id          = NULL;  // int(11)
	var $tag         = NULL;  // string(100)
	var $raw_tag     = NULL;  // string(100)
	var $alias       = NULL;  // string(100)
	var $description = NULL;  // text
	var $admin       = NULL;  // tinyint(3)

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__tags', 'id', $db );
	}
	
	//-----------
	
	public function loadTag( $oid=NULL ) 
	{
		if ($oid === NULL) {
			return false;
		}
		
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE tag='$oid' OR alias='$oid' LIMIT 1" );
		$this->id = $this->_db->loadResult();
		
		return $this->load( $this->id );
	}
	
	//-----------
	
	public function checkExistence() 
	{
		// First see if a normalized tag in this form exists.
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE tag='$this->tag' LIMIT 1" );
		$id = $this->_db->loadResult();
		// If no ID, then see if a raw tag in this form exists.
		if (!$id) {
			$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE raw_tag='$this->raw_tag' LIMIT 1" );
			$id = $this->_db->loadResult();
		}
		// We have an ID = tag exist
		if ($id) {
			return true;
		}
		// Tag doesn't exist
		return false;
	}
	
	//-----------
	
	public function getUsage( $oid=NULL ) 
	{
		if (!$oid) {
			$oid = $this->id;
		}
		if (!$oid) {
			return null;
		}
		
		$to = new TagsObject( $this->_db );
		return $to->getCount( $oid );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->raw_tag ) == '') {
			$this->setError( JText::_('You must enter a tag.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function buildQuery($filters) 
	{
		$filter = '';
		if (isset($filters['by'])) {
			switch ($filters['by']) 
			{
				case 'user':  $filter = "admin=0"; break;
				case 'admin': $filter = "admin=1"; break;
				case 'all':
				default:      $filter = "";        break;
			}
		}

		if (isset($filters['count']) && $filters['count']) {
			$query = "SELECT count(*)";
		} else {
			$query = "SELECT t.id, t.tag, t.raw_tag, t.alias, t.admin, NULL AS total";
		}
		$query .= " FROM $this->_tbl AS t";
		if ($filters['search']) {
			$query .= " WHERE LOWER( t.raw_tag ) LIKE '%".$filters['search']."%'";
			if ($filter) {
				$query .= " AND $filter";
			}
		} else {
			if ($filter) {
				$query .= " WHERE $filter";
			}
		}
		$query .= " ORDER BY t.raw_tag ASC";
		if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		return $query;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$filters['limit'] = 0;
		$filters['count'] = true;
		
		$this->_db->setQuery( $this->buildQuery( $filters ) );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$this->_db->setQuery( $this->buildQuery( $filters ) );
		return $this->_db->loadObjectList();
	}

	//-----------
	
	public function getAutocomplete( $filters=array() ) 
	{
		$query = "SELECT t.id, t.tag, t.raw_tag 
					FROM $this->_tbl AS t 
					WHERE admin=0 AND LOWER( t.raw_tag ) LIKE '".$filters['search']."%' 
					ORDER BY t.raw_tag ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	//-----------
	
	public function getAllTags( $authorized=false ) 
	{
		if (!$authorized) {
			$filter = "WHERE admin=0 ";
		} else {
			$filter = "";
		}
		
		$query = "SELECT id, tag, raw_tag, alias, admin, COUNT(*) as tcount 
				FROM $this->_tbl $filter 
				GROUP BY raw_tag 
				ORDER BY raw_tag ASC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getTopTags( $limit=25, $tbl='', $order='tcount DESC' ) 
	{
		$tj = new TagsObject( $this->_db );
		
		$sql = "SELECT t.tag, t.raw_tag, t.admin, tj.tagid, COUNT(tj.tagid) AS tcount 
				FROM ".$tj->getTableName()." AS tj, $this->_tbl AS t 
				WHERE t.id=tj.tagid AND t.admin=0 
				GROUP BY tagid 
				ORDER BY $order 
				LIMIT $limit";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getRecentTags( $limit=25, $order='taggedon DESC' ) 
	{
		$tj = new TagsObject( $this->_db );

		$sql  = "SELECT t.tag, t.raw_tag, t.admin, tj.taggedon
				FROM $this->_tbl AS t, ".$tj->getTableName()." AS tj
				WHERE t.admin=0 AND tj.tagid = t.id AND t.raw_tag NOT LIKE 'tool:%'
				GROUP BY raw_tag
				ORDER BY $order LIMIT ".$limit;

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getRelatedTags( $id=null, $limit=25 ) 
	{
		if (!$id) {
			$id = $this->id;
		}
		if (!$id) {
			return null;
		}
		
		$this->_db->setQuery( "SELECT objectid, tbl FROM #__tags_object WHERE tagid=".$id );
		$objs = $this->_db->loadObjectList();
		if ($objs) {
			$sql = "SELECT t.* FROM $this->_tbl AS t, #__tags_object AS tg WHERE t.id=tg.tagid AND tg.tagid != ".$id." AND t.admin=0 AND (";
			$s = array();
			foreach ($objs as $obj) 
			{
				$s[] = "(tg.objectid=".$obj->objectid." AND tg.tbl='".$obj->tbl."')";
			}
			$sql .= implode(" OR ",$s);
			$sql .= ") GROUP BY t.id LIMIT ".$limit;
			
			$this->_db->setQuery( $sql );
			return $this->_db->loadObjectList();
		} else {
			return null;
		}
	}
}


class TagsObject extends JTable
{
	var $id       = NULL;  // int(11)
	var $objectid = NULL;  // int(11)
	var $tagid    = NULL;  // int(11)
	var $strength = NULL;  // tinyint(3)
	var $taggerid = NULL;  // int(11)
	var $taggedon = NULL;  // datetime(0000-00-00 00:00:00)
	var $tbl      = NULL;  // varchar(255)

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__tags_object', 'id', $db );
	}
	
	//-----------
	
	public function deleteObjects( $tagid=null ) 
	{
		if (!$tagid) {
			$tagid = $this->tagid;
		}
		if (!$tagid) {
			return false;
		}

		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE tagid='$tagid'" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getCount( $tagid=null ) 
	{
		if (!$tagid) {
			$tagid = $this->tagid;
		}
		if (!$tagid) {
			return false;
		}
		
		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE tagid='$tagid'" );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function moveObjects( $oldtagid=null, $newtagid=null ) 
	{
		if (!$oldtagid) {
			$oldtagid = $this->tagid;
		}
		if (!$oldtagid) {
			return false;
		}
		if (!$newtagid) {
			return false;
		}

		$this->_db->setQuery( "UPDATE $this->_tbl SET tagid='$newtagid' WHERE tagid='$oldtagid'" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function copyObjects( $oldtagid=null, $newtagid=null ) 
	{
		if (!$oldtagid) {
			$oldtagid = $this->tagid;
		}
		if (!$oldtagid) {
			return false;
		}
		if (!$newtagid) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE tagid='$oldtagid'" );
		$rows = $this->_db->loadObjectList();
		if ($rows) {
			foreach ($rows as $row) 
			{
				$to = new TagsObject($this->_db);
				$to->objectid = $row->objectid;
				$to->tagid    = $newtagid;
				$to->strength = $row->strength;
				$to->taggerid = $row->taggerid;
				$to->taggedon = $row->taggedon;
				$to->tbl = $row->tbl;
				$to->store();
			}
		}
		return true;
	}
}

/*
CREATE TABLE `#__tags_group` (
  `id` int(11) NOT NULL auto_increment,
  `groupid` int(11) default '0',
  `tagid` int(11) default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
*/
class TagsGroup extends JTable
{
	var $id      = NULL;  // int(11)
	var $groupid = NULL;  // int(11)
	var $tagid   = NULL;  // int(11)
	var $priority = NULL;  // int(11)
	
	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__tags_group', 'id', $db );
	}

	//-----------
	
	public function getCount() 
	{
		$query = "SELECT COUNT(*) 
					FROM $this->_tbl AS tg,
					#__tags AS t,
					#__xgroups as g
					WHERE tg.tagid=t.id 
					AND g.gidNumber=tg.groupid ORDER BY tg.priority ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
		
	//-----------
	
	public function getRecords() 
	{
		$query = "SELECT tg.id, t.tag, g.cn, g.description, tg.tagid, tg.groupid, tg.priority 
					FROM $this->_tbl AS tg,
					#__tags AS t,
					#__xgroups as g
					WHERE tg.tagid=t.id 
					AND g.gidNumber=tg.groupid ORDER BY tg.priority ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getNeighbor( $move ) 
	{
		switch ($move) 
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE priority < ".$this->priority." ORDER BY priority DESC LIMIT 1";
				break;
			
			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE priority > ".$this->priority." ORDER BY priority ASC LIMIT 1";
				break;
		}
		$this->_db->setQuery( $sql );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}
?>