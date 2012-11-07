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
		
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE raw_tag='$oid' OR tag='$oid' OR alias='$oid' LIMIT 1" );
		$this->id = $this->_db->loadResult();
		
		return $this->load( $this->id );
	}
	
	//-----------
	
	public function delete( $oid=null )
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		$query = 'DELETE FROM #__tags_object WHERE tagid = '. $this->_db->Quote($this->$k);
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$query = 'DELETE FROM #__tags_group WHERE tagid = '. $this->_db->Quote($this->$k);
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return parent::delete($oid);
	}
	
	//-----------
	
	public function checkExistence() 
	{
		// First see if the tag exists.
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE tag='$this->tag' OR raw_tag='$this->raw_tag' LIMIT 1" );
		$id = $this->_db->loadResult();
		// We have an ID = tag exist
		if ($id > 0) {
			return true;
		}
		// Tag doesn't exist
		return false;
	}
	
	//-----------
	
	public function getUsage( $tagid=NULL ) 
	{
		if (!$tagid) {
			$tagid = $this->id;
		}
		if (!$tagid) {
			return null;
		}
		
		$to = new TagsObject( $this->_db );
		return $to->getCount( $tagid );
	}
	
	//-----------
	
	public function getUsageForObject( $tagid=null, $objectid=null, $tbl=null ) 
	{
		$to = new TagsObject( $this->_db );
		return $to->getCountForObject( $tagid, $objectid, $tbl );
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
			$query = "SELECT t.id, t.tag, t.raw_tag, t.alias, t.admin, (SELECT COUNT(*) FROM #__tags_object AS tt WHERE tt.tagid=t.id) AS total";
		}
		$query .= " FROM $this->_tbl AS t";
		if ($filters['search']) {
			// Used to also query using unfiltered search text agains the rawtag and the tag.
			// Figured this was safer
			$query .= " WHERE (LOWER(t.tag) LIKE '%" . $this->normalize($filters['search']) . "%')";
				$query .= " AND $filter";
			}
		} else {
			if ($filter) {
				$query .= " WHERE $filter";
			}
		}
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			if ($filters['sortby'] == 'total') {
				$query .= " ORDER BY ".$filters['sortby']." DESC";
			} else {
				$query .= " ORDER BY t.".$filters['sortby'];
			}
		} else {
			$query .= " ORDER BY t.raw_tag ASC";
		}
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
		$filters['sortby'] = '';
		
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
	
	public function getCloud( $tbl='', $state=0, $objectid=0 ) 
	{
		$tj = new TagsObject( $this->_db );
		
		$sql  = "SELECT t.tag, t.raw_tag, t.alias, t.admin, COUNT(*) as count
				FROM $this->_tbl AS t 
				INNER JOIN ".$tj->getTableName()." AS rt ON (rt.tagid = t.id) AND rt.tbl='$tbl' ";
		if (isset($objectid) && $objectid) {
			$sql .= "WHERE rt.objectid='".$objectid."' ";
		}
		switch ($state) 
		{
			/*case 0:
				$sql .= (isset($objectid) && $objectid) ? "AND (t.state=1 OR t.state=0) " : "WHERE (t.state=1 OR t.state=0) ";
			break;*/
			case 1:
				$sql .= "";
			break;
			case 0:
			default:
				$sql .= (isset($objectid) && $objectid) ? "AND t.admin=0 " : "WHERE t.admin=0 ";
			break;
		}
		$sql .= "GROUP BY raw_tag
				ORDER BY raw_tag ASC";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	//-----------
	
	public function getAutocomplete( $filters=array() ) 
	{
		$query = "SELECT t.id, t.tag, t.raw_tag 
					FROM $this->_tbl AS t 
					WHERE";
		if (isset($filters['admin']) && $filters['admin']) {
			$query .= "";
		} else {
			$query .= " admin=0 AND";
		}
		$query .= " LOWER( t.raw_tag ) LIKE '".$filters['search']."%' 
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
	
	public function getTopTags( $limit=25, $tbl='', $order='tcount DESC', $exclude_private=1) 
	{
		$tj = new TagsObject( $this->_db );
		
		$sql = "SELECT t.tag, t.raw_tag, t.admin, tj.tagid, tj.objectid, COUNT(tj.tagid) AS tcount ";
		$sql.= "FROM $this->_tbl AS t  ";
		$sql.= "JOIN ".$tj->getTableName()." AS tj ON t.id=tj.tagid ";
		if ($exclude_private) {
			$sql.= " LEFT JOIN #__resources AS R ON R.id=tj.objectid AND tj.tbl='resources' ";
			$sql.= " LEFT JOIN #__wiki_page AS P ON P.id=tj.objectid AND tj.tbl='wiki' ";
			$sql.= " LEFT JOIN #__xprofiles AS XP ON XP.uidNumber=tj.objectid AND tj.tbl='xprofiles' ";
		}
		$sql.= "WHERE t.id=tj.tagid AND t.admin=0 ";
		if ($tbl) {
			$sql.= "AND tj.tbl='".$tbl."' ";
		} else {
			$sql.= ($exclude_private) ? " AND ((tj.tbl='resources' AND R.access!=4) OR (tj.tbl='wiki' AND P.access=0) OR (tj.tbl='xprofiles' AND XP.public=0) OR (tj.tbl!='xprofiles' AND tj.tbl!='wiki' AND tj.tbl!='resources' AND tj.tbl!='wishlist' AND tj.tbl!='support') ) " : ""; 
		}
		$sql.= "GROUP BY tagid "; 
		$sql.= "ORDER BY $order ";
		$sql.= "LIMIT $limit";

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

