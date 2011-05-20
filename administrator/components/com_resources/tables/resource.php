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


class ResourcesResource extends JTable 
{
	var $id               = NULL;  // @var int(11) Primary key
	var $title            = NULL;  // @var varchar(250)
	var $type             = NULL;  // @var int(11)
	var $logical_type     = NULL;  // @var int(11)
	var $introtext        = NULL;  // @var text
	var $fulltext         = NULL;  // @var text
	var $footertext       = NULL;  // @var text
	var $created          = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $created_by       = NULL;  // @var int(11)
	var $modified         = NULL;  // @var boolean
	var $modified_by      = NULL;  // @var int(11)
	var $published        = NULL;  // @var int(1)
	var $publish_up       = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $publish_down     = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $access           = NULL;  // @var int(11)
	var $hits             = NULL;  // @var int(11)
	var $path             = NULL;  // @var varchar(200)
	var $checked_out      = NULL;  // @var int(11)
	var $checked_out_time = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $standalone       = NULL;  // @var int(1)
	var $group_owner      = NULL;  // @var varchar(250)
	var $group_access     = NULL;  // @var text
	var $rating           = NULL;  // @var decimal(2,1)
	var $times_rated      = NULL;  // @var int(11)
	var $params           = NULL;  // @var text
	var $attribs          = NULL;  // @var text
	var $alias            = NULL;  // @var varchar(100)
	var $ranking          = NULL;  // @var float
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__resources', 'id', $db );
	}
	
	//-----------
	
	public function loadAlias( $oid=NULL ) 
	{
		if ($oid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE alias='$oid'" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->title ) == '') {
			$this->setError( 'Your Resource must contain a title.' );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getTypeTitle($which=0)
	{
		if ($which) {
			$type = $this->logical_type;
		} else {
			$type = $this->type;
		}
		$this->_db->setQuery( "SELECT type FROM #__resource_types WHERE id=".$type );
		$title = $this->_db->loadResult();
		return ($title) ? $title : '';
	}
	
	//-----------
	
	public function getGroups()
	{
		if ($this->group_access != '') {
			$this->group_access = trim($this->group_access);
			$this->group_access = substr($this->group_access,1,(strlen($this->group_access)-2));
			$allowedgroups = split(';',$this->group_access);
		} else {
			$allowedgroups = array();
		}

		if (!empty($this->group_owner)) {
			$allowedgroups[] = $this->group_owner;
		}
		
		return $allowedgroups;
	}
	
	//-----------

	public function calculateRating()
	{
		$this->_db->setQuery( "SELECT rating FROM #__resource_ratings WHERE resource_id='$this->id'" );
		$ratings = $this->_db->loadObjectList();
	
		$totalcount = count($ratings);
		$totalvalue = 0;
		
		// Add the ratings up
		foreach ($ratings as $item)
		{
			$totalvalue = $totalvalue + $item->rating;
		}
	
		// Find the average of all ratings
		$newrating = ($totalcount > 0) ? $totalvalue / $totalcount : 0;
	
		// Round to the nearest half
		$newrating = ($newrating > 0) ? round($newrating*2)/2 : 0;
	
		// Update page with new rating
		$this->rating = $newrating;
		$this->times_rated = $totalcount;
	}
	
	//-----------
	
	public function updateRating()
	{
		$this->_db->setQuery( "UPDATE $this->_tbl SET rating='$this->rating', times_rated='$this->times_rated' WHERE id='$this->id'" );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit;
		}
	}
	
	//-----------
	
	public function deleteExistence( $id=NULL ) 
	{
		if (!$id) {
			$id = $this->id;
		}
		
		// Delete child associations
		$this->_db->setQuery( "DELETE FROM #__resource_assoc WHERE child_id=".$id );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit;
		}
		// Delete parent associations
		$this->_db->setQuery( "DELETE FROM #__resource_assoc WHERE parent_id=".$id );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit;
		}
		// Delete tag associations
		$this->_db->setQuery( "DELETE FROM #__tags_object WHERE tbl='resources' AND objectid=".$id );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit;
		}
		// Delete ratings
		$this->_db->setQuery( "DELETE FROM #__resource_ratings WHERE resource_id=".$id );
		if (!$this->_db->query()) {
			echo $this->_db->getErrorMsg();
			exit;
		}
	}
	
	//-----------
	
	public function buildQuery( $filters=array() ) 
	{
		$juser =& JFactory::getUser();
		$now = date( 'Y-m-d H:i:s', time() );
		
		$query  = "";
		if (isset($filters['tag']) && $filters['tag'] != '') {
			$query .= "FROM #__tags_object AS RTA ";
			$query .= "INNER JOIN #__tags AS TA ON RTA.tagid = TA.id AND RTA.tbl='resources', $this->_tbl AS C ";
		} else {
			$query .= "FROM $this->_tbl AS C ";
		}
		$query .= "LEFT JOIN #__resource_types AS t ON C.type=t.id ";
		$query .= "LEFT JOIN #__resource_types AS lt ON C.logical_type=lt.id ";
		$query .= "WHERE C.published=1 AND C.standalone=1 ";
		if (isset($filters['type']) && $filters['type'] != '') {
			if ($filters['type'] == 'nontools') {
				$query .= "AND C.type!=7 ";
			} else {
				if ($filters['type'] == 'tools') {
					$filters['type'] = 7;
				}
				$query .= "AND C.type=".$filters['type']." ";
			}
		} else {
			$query .= "AND C.type!=8 ";
		}
		if (isset($filters['minranking']) && $filters['minranking'] != '' && $filters['minranking'] > 0) {
			$query .= "AND C.ranking > ".$filters['minranking']." ";
		}
		$query .= "AND (C.publish_up = '0000-00-00 00:00:00' OR C.publish_up <= '".$now."') ";
		$query .= "AND (C.publish_down = '0000-00-00 00:00:00' OR C.publish_down >= '".$now."') AND ";
		if (isset($filters['access']) && $filters['access'] == 'public') {
			$query .= "(C.access=0 OR C.access=3) ";
		} else if (!$juser->get('guest')) {
			ximport('Hubzero_User_Helper');
			$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'all');
			if ($xgroups != '') {
				$usersgroups = $this->getUsersGroups($xgroups);
				if (count($usersgroups) > 1) {
					$groups = implode("','",$usersgroups);
				} else {
					$groups = count($usersgroups) ? $usersgroups[0] : '';
				}
				$query .= "(C.access=0 OR C.access=1 OR C.access=3 OR (C.access=4 AND (C.group_owner IN ('".$groups."') ";
				foreach ($usersgroups as $group)
				{
					$query .= " OR C.group_access LIKE '%;".$group.";%'";
				}
				$query .= "))) ";
			} else {
				$query .= "(C.access=0 OR C.access=1 OR C.access=3) ";
			}
		} else {
			$query .= "(C.access=0 OR C.access=3) ";
		}
		if (isset($filters['tag']) && $filters['tag'] != '') {
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'tags.php' );
			$tagging = new ResourcesTags( $this->_db );
			$tags = $tagging->_parse_tags($filters['tag']);
		
			$query .= "AND RTA.objectid=C.id AND (TA.tag IN (";
			$tquery = '';
			foreach ($tags as $tagg)
			{
				$tquery .= "'".$tagg."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") OR TA.alias IN (".$tquery;
			$query .= "))";
			$query .= " GROUP BY C.id HAVING uniques=".count($tags);
		}
		$query .= " ORDER BY ";
		if (isset($filters['sortby'])) {
			switch ($filters['sortby']) 
			{
				case 'date':
				case 'date_published':   $query .= 'publish_up DESC';      break;
				case 'date_created':    $query .= 'created DESC';          break;
				case 'date_modified':    $query .= 'modified DESC';        break;
				case 'title':   $query .= 'title ASC, publish_up';         break;
				case 'rating':  $query .= "rating DESC, times_rated DESC"; break;
				case 'ranking': $query .= "ranking DESC";                  break;
				case 'random':  $query .= "RAND()";                        break;
			}
		}
		
		return $query;
	}
	
	//-----------
	
	public function getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) {
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) {
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}
	
	//-----------
	
	public function getCount( $filters=array(), $admin=false ) 
	{
		$query = $this->buildQuery( $filters, $admin );
		
		$sql  = "SELECT C.id";
		$sql .= (isset($filters['tag']) && $filters['tag'] != '') ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques ".$query : " ".$query;

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getRecords( $filters=array(), $admin=false ) 
	{
		$sql  = "SELECT C.id, C.title, C.type, C.introtext, C.fulltext, C.created, C.created_by, C.modified, C.published, C.publish_up, C.standalone, C.access, C.hits, C.rating, C.times_rated, C.params, C.alias, C.ranking, t.type AS typetitle, lt.type AS logicaltitle";
		$sql .= (isset($filters['tag']) && $filters['tag'] != '') ? ", TA.tag, COUNT(DISTINCT TA.tag) AS uniques " : " ";
		$sql .= $this->buildQuery( $filters, $admin );
		$sql .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function buildPluginQuery( $filters=array() )
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'type.php' );
		$rt = new ResourcesType( $database );
		
		if (isset($filters['search']) && $filters['search'] != '') {
			$searchquery = $filters['search'];
			$phrases = $searchquery->searchPhrases;
		}
		if (isset($filters['select']) && $filters['select'] == 'count') {
			if (isset($filters['tags'])) {
				$query = "SELECT count(f.id) FROM (SELECT r.id, COUNT(DISTINCT t.tagid) AS uniques ";
			} else {
				$query = "SELECT count(DISTINCT r.id) ";
			}
		} else {
			$query = "SELECT DISTINCT r.id, r.title, r.alias, r.introtext AS itext, r.fulltext AS ftext, r.published AS state, r.created, r.modified, r.publish_up, r.params, 
					CONCAT( 'index.php?option=com_resources&id=', r.id ) AS href, 'resources' AS section, rt.type AS area, r.type AS category, r.rating, r.times_rated, r.ranking, r.access ";
			if (isset($filters['tags'])) {
				$query .= ", COUNT(DISTINCT t.tagid) AS uniques ";
			}
			if (isset($filters['search']) && $filters['search'] != '') {
				if (!empty($phrases)) {
					$exactphrase = addslashes('"'.$phrases[0].'"');
					$query .= ", ("
							//. "  MATCH(r.introtext,r.fulltext) AGAINST ('$exactphrase' IN BOOLEAN MODE) + r.ranking +"
							. "  MATCH(r.introtext,r.fulltext) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
							. "  MATCH(au.givenName,au.surname) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
							. "  MATCH(r.title) AGAINST ('$exactphrase' IN BOOLEAN MODE)"
							. " ) AS relevance ";
				} else {
					$words = array();
					if (count($searchquery->searchWords) > 0) {
						$ws = $searchquery->searchWords;
						foreach ($ws as $w) 
						{
							if (strlen($w) > 2) {
								$words[] = $w;
							}
						}
					}
					$text = implode(' +',$words);
					$text = addslashes($text);
					
					$text2 = str_replace('+','',$text);

					$query .= ", ("
							//. "  MATCH(r.introtext,r.fulltext) AGAINST ('+$text -\"$text2\"') + r.ranking +"
							. "  MATCH(r.introtext,r.fulltext) AGAINST ('+$text -\"$text2\"') +"
							. "  MATCH(au.givenName,au.surname) AGAINST ('+$text -\"$text2\"') +"
							. "  MATCH(r.title) AGAINST ('+$text -\"$text2\"') + "
							//. "  CASE WHEN LOWER(r.title) LIKE '%$text2%' THEN 5 ELSE 0 END +"
							//. "  CASE WHEN (LOWER(r.title) LIKE '%$text2%' AND LOWER(r.title) NOT LIKE '%lecture%') THEN 10 ELSE 0 END +"
							. "  CASE WHEN (LOWER(r.title) LIKE '%$text2%') THEN 10 ELSE 0 END +"
							//. "  CASE WHEN (LOWER(r.title) LIKE '%$text2%' AND LOWER(r.title) NOT LIKE '%lecture%') THEN 10 ELSE 0 END"
							. "  CASE WHEN (LOWER(r.title) LIKE '%$text2%' AND r.type=6) THEN 50 ELSE 0 END"
							//. "  CASE WHEN (SELECT COUNT(*) FROM #__resource_assoc AS ras WHERE ras.child_id=r.id) > 0 THEN -95 ELSE 0 END"
							. " ) AS relevance ";
				}
			}
			if (isset($filters['sortby']) && ($filters['sortby'] == 'usage' || $filters['sortby'] == 'users')) {
				$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=r.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
			}
			if (isset($filters['sortby']) && $filters['sortby'] == 'jobs') {
				$query .= ", (SELECT rs.jobs FROM #__resource_stats AS rs WHERE rs.resid=r.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS jobs ";
			}
		}
		$query .= "FROM $this->_tbl AS r ";
		$query .= "LEFT JOIN ".$rt->getTableName()." AS rt ON r.type=rt.id ";
		if (isset($filters['search']) && $filters['search'] != '') {
			$query .= "LEFT JOIN #__author_assoc AS aus ON aus.subid=r.id AND aus.subtable='resources' 
						LEFT JOIN #__xprofiles AS au ON aus.authorid=au.uidNumber ";
		}
		if (isset($filters['author'])) {
			//$query .= ", #__author_assoc AS aa ";
			$query .= "LEFT JOIN #__author_assoc AS aa ON aa.subid=r.id AND aa.subtable='resources' ";
		}
		if (isset($filters['favorite'])) {
			$query .= ", #__xfavorites AS xf ";
		}
		if (isset($filters['tag'])) {
			$query .= ", #__tags_object AS t, #__tags AS tg ";
		}
		if (isset($filters['tags'])) {
			$query .= ", #__tags_object AS t ";
			//$query .= " INNER JOIN #__tags AS tg ON (t.tagid = tg.id)";
		}
		$query .= "WHERE r.standalone=1 ";
		if ($juser->get('guest') || (isset($filters['authorized']) && !$filters['authorized'])) {
			$query .= "AND r.published=1 ";
		}
		if (isset($filters['author'])) {
			$query .= "AND (aa.authorid='". $filters['author'] ."') "; // "' OR r.created_by=". $filters['author'] .") "; - SS - globalHub #622 - Mourad was the creator of a bunch of resources he was not listed as a contributor to in jos_author_assoc, making his profile page look wildly incorrect

		}
		if (isset($filters['favorite'])) {
			$query .= "AND xf.uid='". $filters['favorite'] ."' AND r.id=xf.oid AND xf.tbl='resources' ";
		}
		if (isset($filters['tag'])) {
			$query .= "AND t.objectid=r.id AND t.tbl='resources' AND t.tagid=tg.id AND (tg.tag='".$filters['tag']."' OR tg.alias='".$filters['tag']."') ";
		}
		if (isset($filters['tags'])) {
			$ids = implode(',',$filters['tags']);
			$query .= "AND t.objectid=r.id AND t.tbl='resources' AND t.tagid IN (".$ids.") ";
		}
		if (isset($filters['type']) && $filters['type'] != '') {
			$query .= "AND r.type=".$filters['type']." ";
		}
		
		if (isset($filters['group']) && $filters['group'] != '') {
			$query .= "AND (r.group_owner='".$filters['group']."' OR r.group_access LIKE '%;".$filters['group'].";%') ";
			if (!$filters['authorized']) {
				switch ($filters['access']) 
				{
					case 'public':    $query .= "AND r.access = 0 ";  break;
					case 'protected': $query .= "AND r.access = 3 ";  break;
					case 'private':
					case 'all':
					default:          $query .= "AND r.access != 4 "; break;
				}
			} else {
				switch ($filters['access']) 
				{
					case 'public':    $query .= "AND r.access = 0 ";  break;
					case 'protected': $query .= "AND r.access = 3 ";  break;
					case 'private':   $query .= "AND r.access = 4 ";  break;
					case 'all':
					default:          $query .= ""; break;
				}
			}
		} else {
			if (!$juser->get('guest')) {
				if (!isset($filters['usergroups'])) {
					ximport('Hubzero_User_Helper');
					$xgroups = Hubzero_User_Helper::getGroups($juser->get('id'), 'all');
				} else {
					$xgroups = $filters['usergroups'];
				}
				if (isset($filters['author'])) {
					//$query .= "AND (r.access=0 OR r.access=1 OR r.access=3) ";
				} else if ($xgroups != '') {
					$usersgroups = $this->getUsersGroups($xgroups);
					if (count($usersgroups) > 1) {
						$groups = implode("','",$usersgroups);
					} else {
						$groups = count($usersgroups) ? $usersgroups[0] : '';
					}
					$query .= "AND (r.access=0 OR r.access=1 OR r.access=3 OR (r.access=4 AND (r.group_owner IN ('".$groups."') ";
					/*foreach ($usersgroups as $group)
					{
						$query .= " OR r.group_access LIKE '%;".$group.";%'";
					}*/
					$query .= " OR r.created_by=".$juser->get('id');
					$query .= "))) ";
				} else {
					$query .= "AND (r.access=0 OR r.access=1 OR r.access=3) ";
				}
			} else {
				$query .= "AND (r.access=0 OR r.access=3) ";
			}
		}
		
		if (isset($filters['now'])) {
			$query .= "AND (r.publish_up = '0000-00-00 00:00:00' OR r.publish_up <= '".$filters['now']."') ";
			$query .= "AND (r.publish_down = '0000-00-00 00:00:00' OR r.publish_down >= '".$filters['now']."') ";
		}
		if (isset($filters['startdate'])) {
			$query .= "AND r.publish_up > '".$filters['startdate']."' ";
		}
		if (isset($filters['enddate'])) {
			$query .= "AND r.publish_up < '".$filters['enddate']."' ";
		}
		
		if (isset($filters['search']) && $filters['search'] != '') {
			if (!empty($phrases)) {
				$exactphrase = addslashes('"'.$phrases[0].'"');
				$query .= "AND ( (MATCH(r.title) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR"
						 . " (MATCH(au.givenName,au.surname) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR "
						 . " (MATCH(r.introtext,r.fulltext) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) ) ";
						
			} else {
				$words = array();
				if (count($searchquery->searchWords) > 0) {
					$ws = $searchquery->searchWords;
					foreach ($ws as $w) 
					{
						if (strlen($w) > 2) {
							$words[] = $w;
						}
					}
				}
				$text = implode(' +',$words);
				//$text = implode(' ',$searchquery->searchWords);
				$text = addslashes($text);
				$text2 = str_replace('+','',$text);
				
				$query .= "AND ( (MATCH(r.title) AGAINST ('+$text -\"$text2\"') > 0) OR"
						 . " (MATCH(au.givenName,au.surname) AGAINST ('+$text -\"$text2\"') > 0) OR "
						 . " (MATCH(r.introtext,r.fulltext) AGAINST ('+$text -\"$text2\"') > 0) ) ";
						
			}
			if (isset($filters['limit']) && $filters['limit'] != 'all') {
				$query .= "GROUP BY r.id ";
			}
		}
		if (isset($filters['tags'])) {
			$query .= " GROUP BY r.id HAVING uniques=".count($filters['tags'])." ";
		}
		if (isset($filters['select']) && $filters['select'] != 'count') {
			if (isset($filters['sortby'])) {
				if (isset($filters['groupby'])) {
					$query .= "GROUP BY r.id ";
				}
				$query .= "ORDER BY ";
				switch ($filters['sortby']) 
				{
					case 'date':    $query .= 'publish_up DESC';               break;
					case 'title':   $query .= 'title ASC, publish_up';         break;
					case 'rating':  $query .= "rating DESC, times_rated DESC"; break;
					case 'ranking': $query .= "ranking DESC";                  break;
					case 'relevance': $query .= "relevance DESC";              break;
					case 'users':
					case 'usage':   $query .= "users DESC";              break;
					case 'jobs':   $query .= "jobs DESC";              break;
				}
			}
			if (isset($filters['limit']) && $filters['limit'] != 'all') {
				$query .= " LIMIT ".$filters['limitstart'].",".$filters['limit'];
			}
		}
		if (isset($filters['select']) && $filters['select'] == 'count') {
			if (isset($filters['tags'])) {
				$query .= ") AS f";
			}
		}
//echo '<!-- '.$query.' -->';
		return $query;
	}
}

