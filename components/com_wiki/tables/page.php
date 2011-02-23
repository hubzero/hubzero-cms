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

class WikiPage extends JTable 
{
	var $id          = NULL;  // @var int(11) Primary key
	var $pagename    = NULL;  // @var varchar(100) Unique key
	var $hits        = NULL;  // @var int(11)
	var $created_by  = NULL;  // @var int(11)
	var $rating      = NULL;  // @var decimal(2,1)
	var $times_rated = NULL;  // @var int(11)
	var $title       = NULL;  // @var varchar(255)
	var $scope       = NULL;  // @var varchar(255)
	var $params      = NULL;  // @var text
	var $ranking     = NULL;  // @var float
	var $authors     = NULL;  // @var varchar(255)
	var $access      = NULL;  // @var tinyint(2)
	var $group       = NULL;  // @var varchar(255)
	var $state       = NULL;  // @var tinyint(2)
	
	//-----------
	
	function __construct( &$db ) 
	{
		parent::__construct( '#__wiki_page', 'id', $db );
	}
	
	//-----------
	
	function load( $oid=NULL, $scope='' ) 
	{
		$s = "";
		if ($oid !== NULL && !is_numeric($oid)) {
			$this->_tbl_key = 'pagename';
			$s = "AND scope='$scope'";
		}
		$k = $this->_tbl_key;
		if ($oid !== NULL) {
			$this->$k = $oid;
		}
		$oid = $this->$k;
		if ($oid === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE $this->_tbl_key='$oid' $s" );
		//return $this->_db->loadObject( $this );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	function getID() 
	{
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE pagename='". $this->pagename ."' AND scope='".$this->scope."'" );
		$this->id = $this->_db->loadResult();
	}
	
	//-----------
	
	function exist()
	{
		if ($this->id !== NULL) {
			return true;
		} else {
			return false;
		}
	}
	
	//-----------

	function calculateRating()
	{
		$this->_db->setQuery( "SELECT rating FROM #__wiki_comments WHERE pageid='$this->id' AND rating!=0" );
		$ratings = $this->_db->loadObjectList();

		$totalcount = count($ratings);
		$totalvalue = 0;
		
		// Add the ratings up
		foreach ($ratings as $item)
		{
			$totalvalue = $totalvalue + $item->rating;
		}
	
		// Find the average of all ratings
		$newrating = $totalvalue / $totalcount;
	
		// Round to the nearest half
		$newrating = round($newrating*2)/2;
	
		// Update page with new rating
		$this->rating = $newrating;
		$this->times_rated = $totalcount;
	}
	
	//-----------
	
	function getTags($admin=0) 
	{
		$obj = new WikiTags( $this->_db );
		$tags = $obj->get_tags_on_object($this->id, 0, 0, '', 0, $admin);
		return $tags;
	}
	
	//-----------
	
	function getRevision($version)
	{
		$obj = new WikiPageRevision( $this->_db );
		$obj->loadByVersion( $this->id, $version );
		return $obj;
	}
	
	public function getRevisionCount() 
	{
		$obj = new WikiPageRevision( $this->_db );
		$obj->pageid = $this->id;
		return $obj->getRevisionCount();
	}
	
	//-----------
	
	function getCurrentRevision()
	{
		$obj = new WikiPageRevision( $this->_db );
		$obj->loadByVersion( $this->id );
		return $obj;
	}
	
	//-----------
	
	function getTemplates()
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE `pagename` LIKE 'Template:%' AND `group`='$this->group' ORDER BY `pagename`" );
		return $this->_db->loadObjectList();
	}
	
	//-----------

	function _check($pagename) 
	{
		// Compress internal white-space to single space character.
		$pagename = preg_replace('/[\s\xa0]+/', ' ', $orig = $pagename);
		
		// Strip leading and trailing white-space.
		$pagename = trim($pagename);
	
		$orig = $pagename;
		while ($pagename and $pagename[0] == WIKI_SUBPAGE_SEPARATOR) 
		{
			$pagename = substr($pagename, 1);
		}
		
		if ($pagename != $orig) {
			$this->setError( sprintf("Pagename: Leading %s not allowed", WIKI_SUBPAGE_SEPARATOR) );
		}
			
		// not only for SQL, also to restrict url length
		if (strlen($pagename) > WIKI_MAX_PAGENAME_LENGTH) {
			$pagename = substr($pagename, 0, WIKI_MAX_PAGENAME_LENGTH);
			$this->setError( 'Pagename too long' );
		}
		
		return $pagename;
	}

	//-----------

	function check() 
	{
		if (is_string($this->pagename) && trim($this->pagename) == '') {
			$this->setError( 'Your page must have a name.' );
			return false;
		}
		$this->pagename = $this->_check( $this->pagename );

		if (trim($this->getError()) != '') {
			return false;
		}
		return true;
	}
	
	//-----------
	
	function getAuthors()
	{
		$auths = explode(',',$this->authors);
		for ($i=0, $n=count( $auths ); $i < $n; $i++) 
		{
			$auths[$i] = trim($auths[$i]);
		}
		
		return $auths;
	}
	
	//-----------
	
	function deleteBits( $id=NULL ) 
	{
		if (!$id) {
			$id = $this->id;
		}
		
		// Delete the page's version history
		$this->_db->setQuery( "DELETE FROM #__wiki_version WHERE pageid='".$id."'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
		// Delete the page's tags
		$this->_db->setQuery( "DELETE FROM #__tags_object WHERE tbl='wiki' AND objectid='".$id."'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
		// Delete the page's comments
		$this->_db->setQuery( "DELETE FROM #__wiki_comments WHERE pageid='".$id."'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
		// Delete the page's attachments
		$this->_db->setQuery( "DELETE FROM #__wiki_attachments WHERE pageid='".$id."'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
		// Delete the page's authors
		/*$this->_db->setQuery( "DELETE FROM #__wiki_authors WHERE pid='".$id."'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}*/
	}
	
	//-----------
	
	function count( $filters=array() )
	{
		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl" );
		return $this->_db->loadResult();
	}
	
	public function getPagesCount( $filters=array() )
	{
		$filters['limit'] = 0;
		$filters['count'] = true;
		
		$this->_db->setQuery( $this->buildQuery( $filters ) );
		return $this->_db->loadResult();
	}
	
	public function getPages( $filters=array() )
	{
		//echo $this->buildQuery( $filters );
		$this->_db->setQuery( $this->buildQuery( $filters ) );
		return $this->_db->loadObjectList();
	}
	
	public function buildQuery($filters) 
	{
		if (isset($filters['count']) && $filters['count']) {
			$query = "SELECT count(*)";
		} else {
			$query = "SELECT t.*, (SELECT COUNT(*) FROM #__wiki_version AS tt WHERE tt.pageid=t.id) AS revisions";
		}
		$query .= " FROM $this->_tbl AS t";
		if ($filters['search']) {
			$query .= " WHERE (LOWER( t.pagename ) LIKE '%".$filters['search']."%' OR LOWER( t.title ) LIKE '%".$filters['search']."%')";
		}
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY t.".$filters['sortby'];
		} else {
			$query .= " ORDER BY t.id ASC GROUP BY `t.group`";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		return $query;
	}
	
	//-----------
	
	function buildPluginQuery( $filters=array() ) 
	{
		//$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		//include_once( JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'wiki'.DS.'revision.php' );
		//$wr = new WikiPageRevision( $database );
		if (isset($filters['search']) && $filters['search'] != '') {
			$searchquery = $filters['search'];
			$phrases = $searchquery->searchPhrases;
		}
		if (isset($filters['select']) && $filters['select'] == 'count') {
			if (isset($filters['tags'])) {
				$query = "SELECT COUNT(f.id) FROM (SELECT v.pageid AS id, COUNT(DISTINCT t.tagid) AS uniques ";
			} else {
				$query = "SELECT COUNT(*) FROM (SELECT COUNT(DISTINCT v.pageid) ";
			}
		} else {
			$query = "SELECT v.pageid AS id, w.title, w.pagename AS alias, v.pagehtml AS itext, NULL AS ftext, w.state, v.created, v.created AS modified, v.created AS publish_up, w.params, 
					CONCAT( 'index.php?option=com_topics&pagename=', w.pagename ) AS href, 'topics' AS section, w.group AS area, w.scope AS category, w.rating, w.times_rated, w.ranking, w.access, w.hits ";
			if (isset($filters['tags'])) {
				$query .= ", COUNT(DISTINCT t.tagid) AS uniques ";
			}
			if (isset($filters['search']) && $filters['search'] != '') {
				if (!empty($phrases)) {
					$exactphrase = addslashes('"'.$phrases[0].'"');
					$text3 = preg_replace("/[^a-zA-Z0-9]/", "", strtolower($searchquery->searchText));
					$query .= ", ("
							. "  MATCH(v.pagetext) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
							. "  MATCH(w.title) AGAINST ('$exactphrase' IN BOOLEAN MODE) +"
							. "  CASE WHEN LOWER(w.pagename) LIKE '%$text3%' THEN 10 ELSE 0 END"
							. " ) AS relevance ";
				} else {
					/*$words = array();
					if (count($searchquery->searchWords) > 0) {
						$ws = $searchquery->searchWords;
						foreach ($ws as $w) 
						{
							if (strlen($w) > 2) {
								$words[] = $w;
							}
						}
					}
					$text = implode(' +',$words);*/
					$text = implode(' +',$searchquery->searchWords);
					$text = addslashes($text);
					//$text2 = str_replace('+','',$text);
					//$text3 = str_replace(' ','',$text2);
					$text2 = preg_replace("/[^a-zA-Z0-9\s]/", "", strtolower($searchquery->searchText));
					$text3 = preg_replace("/[^a-zA-Z0-9]/", "", strtolower($searchquery->searchText));
					
					$query .= ", ("
							. "  MATCH(v.pagetext) AGAINST ('+$text -\"$text2\"') +"
							. "  MATCH(w.title) AGAINST ('+$text -\"$text2\"') +"
							. "  CASE WHEN LOWER(w.title) LIKE '%$text2%' THEN 5 ELSE 0 END +"
							. "  CASE WHEN LOWER(w.pagename) LIKE '%$text3%' THEN 50 ELSE 0 END"
							. " ) AS relevance ";
				}
			}
		}
		$query .= "FROM $this->_tbl AS w, #__wiki_version AS v ";
		if (isset($filters['tags'])) {
			$query .= ", #__tags_object AS t ";
		}
		$query .= "WHERE w.id=v.pageid AND v.approved=1 ";
		if (isset($filters['author'])) {
			$query .= "AND (w.created_by='".$filters['author']."'";
			if (isset($filters['username']) && trim($filters['username']) != '') {
				$query .= " OR w.authors LIKE '%".$filters['username']."%'";
			}
			$query .= ") ";
		}
		if (isset($filters['tags'])) {
			$ids = implode(',',$filters['tags']);
			$query .= "AND w.id=t.objectid AND t.tbl='wiki' AND t.tagid IN ($ids) ";
		}
		if (isset($filters['search']) && $filters['search'] != '') {
			if (!empty($phrases)) {
				$exactphrase = addslashes('"'.$phrases[0].'"');
				$query .= "AND ( (MATCH(w.title) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) OR"
						 . " (MATCH(v.pagetext) AGAINST ('$exactphrase' IN BOOLEAN MODE) > 0) ) ";
						
			} else {
				/*$words = array();
				if (count($searchquery->searchWords) > 0) {
					$ws = $searchquery->searchWords;
					foreach ($ws as $w) 
					{
						if (strlen($w) > 2) {
							$words[] = $w;
						}
					}
				}
				$text = implode(' +',$words);*/
				$text = implode(' +',$searchquery->searchWords);
				$text = addslashes($text);
				//$text2 = str_replace('+','',$text);
				$text2 = preg_replace("/[^a-zA-Z0-9\s]/", "", strtolower($searchquery->searchText));
				$text3 = preg_replace("/[^a-zA-Z0-9]/", "", strtolower($searchquery->searchText));
				
				$query .= "AND ( ( MATCH(w.title) AGAINST ('+$text -\"$text2\"') > 0) OR"
						 . " ( MATCH(v.pagetext) AGAINST ('+$text -\"$text2\"') > 0) ) ";
						
			}
		}
		if (isset($filters['group']) && $filters['group'] != '') {
			$query .= "AND (r.group_owner='".$filters['group']."') ";
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
				if (isset($filters['authorized']) && $filters['authorized'] === 'admin') {
					$query .= "";
				} else {
					ximport('Hubzero_User_Helper');

					$ugs = Hubzero_User_Helper::getGroups( $juser->get('id'), 'members' );
					$groups = array();
					if ($ugs && count($ugs) > 0) {
						foreach ($ugs as $ug) 
						{
							$groups[] = $ug->cn;
						}
					}
					$g = "'".implode("','",$groups)."'";

					$query .= "AND (w.access!=1 OR (w.access=1 AND (w.group IN ($g) OR w.created_by='".$juser->get('id')."'))) ";
				}
			} else {
				$query .= "AND w.access!=1 ";
			}
		}
		if (isset($filters['startdate'])) {
			$query .= "AND v.created > '".$filters['startdate']."' ";
		}
		if (isset($filters['enddate'])) {
			$query .= "AND v.created < '".$filters['enddate']."' ";
		}
		$query .= "GROUP BY pageid ";
		if (isset($filters['tags'])) {
			$query .= "HAVING uniques=".count($filters['tags'])." ";
		}
		if (isset($filters['select']) && $filters['select'] != 'count') {
			if (isset($filters['sortby'])) {
				$query .= "ORDER BY ";
				switch ($filters['sortby']) 
				{
					case 'title':   $query .= 'title ASC';         break;
					case 'id':  $query .= "id DESC"; break;
					case 'rating': $query .= "rating DESC";                  break;
					case 'ranking': $query .= "ranking DESC";                  break;
					case 'relevance': $query .= "relevance DESC";              break;
					case 'usage':
					case 'hits':    $query .= 'hits DESC';               break;
					case 'date':
					default: $query .= 'created DESC';               break;
				}
			}
			if (isset($filters['limit']) && $filters['limit'] != 'all') {
				$query .= " LIMIT ".$filters['limitstart'].",".$filters['limit'];
			}
		}
		if (isset($filters['select']) && $filters['select'] == 'count') {
			//if (isset($filters['tags'])) {
				$query .= ") AS f";
			//}
		}
		//echo '<!-- '.$query.' -->';
		return $query;
	}
}
