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

require_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.class.php' );

//----------------------------------------------------------
//  Resources Tagging class
//----------------------------------------------------------

class ResourcesTags extends Tags
{
	public function __construct( $db, $config=array() )
	{
		$this->_db  = $db;
		$this->_tbl = 'resources';
		
		if (isset($config['normalized_valid_chars'])) {
			$this->_normalized_valid_chars = $config['normalized_valid_chars'];
		}
		if (isset($config['normalize_tags'])) {
			$this->_normalize_tags = $config['normalize_tags'];
		}
		if (isset($config['max_tag_length'])) {
			$this->_max_tag_length = $config['max_tag_length'];
		}
		if (isset($config['block_multiuser_tag_on_object'])) {
			$this->_block_multiuser_tag_on_object = $config['block_multiuser_tag_on_object'];
		}
	}
	
	//-----------
	
	public function getTags($id, $tagger_id=0, $strength=0, $admin=0)
	{
		$sql = "SELECT DISTINCT t.* FROM $this->_tag_tbl AS t, $this->_obj_tbl AS rt WHERE rt.objectid=$id AND rt.tbl='$this->_tbl' AND rt.tagid=t.id";
		if ($admin == 1) {
			$sql .= "";
		} else {
			$sql .= " AND t.admin=0";
		}
		if ($tagger_id != 0) {
			$sql .= " AND rt.taggerid=".$tagger_id;
		}
		if ($strength) {
			$sql .= " AND rt.strength=".$strength;
		}
		$sql .= " ORDER BY t.raw_tag";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function get_tags_with_objects($id=0, $type=0, $tag='') 
	{
		$juser =& JFactory::getUser();
		$now  = date( 'Y-m-d H:i:s', time() );
		
		$this->_db->setQuery( "SELECT objectid FROM $this->_tag_tbl AS t, $this->_obj_tbl AS o WHERE o.tagid=t.id AND t.tag='$tag' AND o.tbl='$this->_tbl'" );
		$objs = $this->_db->loadObjectList();
		$ids = '';
		if ($objs) {
			$s = array();
			foreach ($objs as $obj) 
			{
				$s[] = $obj->objectid;
			}
			$ids = implode(",",$s);
		}
		
		$sql = "SELECT t.id, t.tag, t.raw_tag, r.id AS rid, 0 AS ucount, NULL AS rids 
				FROM $this->_tag_tbl AS t, $this->_obj_tbl AS o, #__resources AS r
				WHERE o.tbl='$this->_tbl' 
				AND o.tagid=t.id 
				AND t.admin=0 
				AND o.objectid=r.id 
				AND r.published=1 
				AND r.standalone=1
				AND (r.publish_up = '0000-00-00 00:00:00' OR r.publish_up <= '$now') 
				AND (r.publish_down = '0000-00-00 00:00:00' OR r.publish_down >= '$now') ";
		if ($type) {
			$sql .= "AND r.type=".$type." ";
		}
		$sql .= (!$juser->get('guest'))     
			  ? "AND (r.access=0 OR r.access=1) " 
			  : "AND r.access=0 ";
		if ($ids) {
			$sql .= "AND o.objectid IN ($ids) ";
		}
		$sql .= "ORDER BY t.raw_tag ASC";

		$this->_db->setQuery( $sql );
		$results = $this->_db->loadObjectList();
		
		$rows = array();
		if ($results) {
			foreach ($results as $result) 
			{
				if (!isset($rows[$result->id])) {
					$rows[$result->id] = $result;
					$rows[$result->id]->ucount++;
					$rows[$result->id]->rids = array($result->rid);
				} else {
					if (!in_array($result->rid,$rows[$result->id]->rids)) {
						$rows[$result->id]->ucount++;
						$rows[$result->id]->rids[] = $result->rid;
					}
				}
			}
		}
		return $rows;
	}
	
	//-----------
	
	public function get_objects_on_tag( $tag='', $id=0, $type=0, $sortby='title', $tag2='', $filterby=array() ) 
	{
		$juser =& JFactory::getUser();
		$now  = date( 'Y-m-d H:i:s', time() );
		
		if ($tag || $tag2) {
			$query  = "SELECT C.id, TA.tag, COUNT(DISTINCT TA.tag) AS uniques, ";
			if ($type == 7) {
				$query.= "TV.title ";
			} else {
				$query.= "C.title ";
			}
			switch ($sortby) 
			{
				case 'users':
					$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
				break;
				case 'jobs':
					$query .= ", (SELECT rs.jobs FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS jobs ";
				break;
			}
			$query .= "FROM #__resources AS C ";
			if ($id) {
				$query .= "INNER JOIN #__resource_assoc AS RA ON (RA.child_id = C.id AND RA.parent_id=".$id.")";
			}
			if ($type == 7) {
				//$query .= " JOIN #__tool AS TL ON C.alias=TL.toolname JOIN #__tool_version as TV on TV.toolid=TL.id AND TV.state=1 AND TV.revision = (SELECT MAX(revision) FROM #__tool_version as TV WHERE TV.toolid=TL.id AND TV.state=1 GROUP BY TV.toolid) ";
				if (!empty($filterby)) {
					$query .= " LEFT JOIN #__resource_taxonomy_audience AS TTA ON C.id=TTA.rid ";
				}
				$query .= ", #__tool_version as TV ";
			}
			/*if ($id) {
				$query .= "INNER JOIN #__resource_assoc AS RA ON (RA.child_id = C.id AND RA.parent_id=".$id.")";
			}*/
			$query .= ", $this->_obj_tbl AS RTA INNER JOIN #__tags AS TA ON (RTA.tagid = TA.id) ";
		} else {
			$query  = "SELECT C.id,  ";
			if ($type == 7) {
				$query.= "TV.title ";
			} else {
				$query.= "C.title ";
			}
			switch ($sortby) 
			{
				case 'users':
					$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=12 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
				break;
				case 'jobs':
					$query .= ", (SELECT rs.jobs FROM #__resource_stats AS rs WHERE rs.resid=C.id AND rs.period=12 ORDER BY rs.datetime DESC LIMIT 1) AS jobs ";
				break;
			}
			$query .= "FROM #__resources AS C ";
			if ($id) {
				$query .= "INNER JOIN #__resource_assoc AS RA ON (RA.child_id = C.id AND RA.parent_id=".$id.")";
			}
			if ($type == 7) {
				if (!empty($filterby)) {
					$query .= " LEFT JOIN #__resource_taxonomy_audience AS TTA ON C.id=TTA.rid ";
				}
				$query .= ", #__tool_version as TV ";
			}
		}
		
		$query .= "WHERE C.published=1 AND C.standalone=1 ";
		if ($type) {
			$query .= "AND C.type=".$type." ";
		}
		if ($type == 7) {
			$query .= " AND TV.toolname=C.alias AND TV.state=1 AND TV.revision = (SELECT MAX(revision) FROM #__tool_version as TV WHERE TV.toolname=C.alias AND TV.state=1 GROUP BY TV.toolid) ";
		}
		if (!empty($filterby) && $type == 7) {
			$fquery = " AND ((";
			for ($i=0, $n=count( $filterby ); $i < $n; $i++) 
			{
				$fquery .= " TTA.".$filterby[$i]." = '1'";
				$fquery .= ($i + 1) == $n ? "" : " OR ";
			}
			$fquery .= ") OR (";
			for ($i=0, $n=count( $filterby ); $i < $n; $i++) 
			{
				$fquery .= " TTA.".$filterby[$i]." IS NULL";
				$fquery .= ($i + 1) == $n ? "" : " OR ";
			}
			$fquery .= "))";
			$query .= $fquery;
		}
		$query .= "AND (C.publish_up = '0000-00-00 00:00:00' OR C.publish_up <= '".$now."') ";
		$query .= "AND (C.publish_down = '0000-00-00 00:00:00' OR C.publish_down >= '".$now."') AND ";
		$query .= (!$juser->get('guest'))     
			   ? "(C.access=0 OR C.access=1) " 
			   : "(C.access=0) ";
		if ($tag || $tag2) {
			if ($tag && !$tag2) {
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_tbl' AND (TA.tag IN ('".$tag."'))";
				$query .= " GROUP BY C.id HAVING uniques=1";
			} else if ($tag2 && !$tag) {
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_tbl' AND (TA.tag IN ('".$tag2."'))";
				$query .= " GROUP BY C.id HAVING uniques=1";
			} else if ($tag && $tag2) {
				$query .= "AND RTA.objectid=C.id AND RTA.tbl='$this->_tbl' AND (TA.tag IN ('".$tag."','".$tag2."'))";
				$query .= " GROUP BY C.id HAVING uniques=2";
			}
		}
		switch ($sortby) 
		{
			case 'ranking':
				$sort = "ranking DESC";
			break;
			case 'date':
				$sort = "publish_up DESC";
			break;
			case 'users':
				$sort = "users DESC";
			break;
			case 'jobs':
				$sort = "jobs DESC";
			break;
			default:
			case 'title':
				$sort = "title ASC";
			break;
		}
		$query .= " ORDER BY ".$sort.", publish_up";
		//echo '<!-- '.$query.' -->';
		$this->_db->setQuery( $query );
		$rows = $this->_db->loadObjectList();
		
		/*if ($rows) {
			if (!empty($filterby) && $type == 7) {
				$results = array();
				foreach ($rows as $key => $row)
				{
					$inc = false;
					for ($i=0, $n=count( $filterby ); $i < $n; $i++) 
					{
						if (isset($row->$filterby[$i]) && $row->$filterby[$i] == 1) {
							$inc = true;
						}
					}
					if ($inc) {
						$results[] = $row;
					}
				}
				$rows = $results;
			}
		}*/
		
		return $rows;
	}
	
	//-----------
	
	public function checkTagUsage( $tag, $id=0, $alias='' ) 
	{
		if (!$id && !$alias) {
			return false;
		}
		if ($id) {
			$query = "SELECT COUNT(*) FROM $this->_obj_tbl AS ta, $this->_tag_tbl AS t WHERE ta.tagid=t.id AND t.tag='".$tag."' AND ta.tbl='resources' AND ta.objectid=".$id;
		}
		if (!$id && $alias) {
			$query = "SELECT COUNT(*) 
						FROM $this->_obj_tbl AS ta, $this->_tag_tbl AS t, #__resources AS r 
						WHERE ta.tagid=t.id 
						AND t.tag='".$tag."' 
						AND ta.tbl='resources' 
						AND ta.objectid=r.id 
						AND r.alias='".$alias."'";
		}
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getTagUsage( $tag, $rtrn='id' ) 
	{
		if (!$tag) {
			return array();
		}

		$query = "SELECT r.$rtrn 
					FROM $this->_obj_tbl AS ta, $this->_tag_tbl AS t, #__resources AS r 
					WHERE ta.tagid=t.id 
					AND t.tag='".$tag."' 
					AND ta.tbl='resources' 
					AND ta.objectid=r.id";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResultArray();
	}
}
?>