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
// Citations class
//----------------------------------------------------------

class CitationsCitation extends JTable
{
	var $id             = NULL;  // @var int(11) Primary key
	var $uid            = NULL;  // @var varchar(200)
	var $affiliated     = NULL;  // @var int(3)
	var $fundedby       = NULL;  // @var int(3)
	var $created        = NULL;  // @var datetime
	var $address        = NULL;  // @var varchar(250)
	var $author         = NULL;  // @var varchar(250)
	var $booktitle      = NULL;  // @var varchar(250)
	var $chapter        = NULL;  // @var varchar(250)
	var $cite           = NULL;  // @var varchar(250)
	var $edition        = NULL;  // @var varchar(250)
	var $editor         = NULL;  // @var varchar(250)
	var $eprint         = NULL;  // @var varchar(250)
	var $howpublished   = NULL;  // @var varchar(250)
	var $institution    = NULL;  // @var varchar(250)
	var $isbn           = NULL;  // @var varchar(50)
	var $journal        = NULL;  // @var varchar(250)
	var $key            = NULL;  // @var varchar(250)
	var $location       = NULL;  // @var varchar(250)
	var $month          = NULL;  // @var int(2)
	var $note           = NULL;  // @var text
	var $number         = NULL;  // @var int(11)
	var $organization   = NULL;  // @var varchar(250)
	var $pages          = NULL;  // @var varchar(250)
	var $publisher      = NULL;  // @var varchar(250)
	var $school         = NULL;  // @var varchar(250)
	var $series         = NULL;  // @var varchar(250)
	var $title          = NULL;  // @var varchar(250)
	var $type           = NULL;  // @var varchar(250)
	var $url            = NULL;  // @var varchar(250)
	var $volume         = NULL;  // @var int(11)
	var $year           = NULL;  // @var int(4)
	var $doi            = NULL;  // @var varchar(50)
	var $ref_type       = NULL;  // @var varchar(50)
	var $date_submit    = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $date_accept    = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $date_publish   = NULL;  // @var datetime(0000-00-00 00:00:00)
	
	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__citations', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->title ) == '') {
			$this->_error = JText::_('CITATION_MUST_HAVE_TITLE');
			return false;
		}
		return true;
	}
	
	//-----------
	
	function getCount( $filter=array(), $admin=true ) 
	{
		$query = "SELECT count(*) FROM $this->_tbl AS r";
		if ($admin) {
			if (isset($filter['search'])) {
				$query .= "\n WHERE (r.title LIKE '%".$filter['search']."%'";
				$query .= "\n OR r.author LIKE '%".$filter['search']."%'";
				if (is_numeric($filter['search'])) {
					$query .= "\n OR r.id=".$filter['search'];
				}
				$query .= ")";
			}
		} else {
			$query .= " WHERE r.id!=0";
			if (isset($filter['type']) && $filter['type']!='') {
				$query .= " AND r.type='".$filter['type']."'";
			}
			if (isset($filter['filter'])) {
				switch ($filter['filter'])
				{
					case 'aff':
						$query .= " AND affiliated=1";
						break;
					case 'nonaff':
						$query .= " AND affiliated=0";
						break;
					default:
						$query .= "";
						break;
				}
			}
			if (isset($filter['year']) && is_numeric($filter['year']) && $filter['year'] > 0) {
				$query .= " AND r.year='".$filter['year']."'";
			}
			if (isset($filter['search']) && $filter['search']!='') {
				$query .= ($filter['search']) ? " AND (LOWER(r.title) LIKE '%".strtolower($filter['search'])."%' OR LOWER(r.journal) LIKE '%".strtolower($filter['search'])."%')" : "";
			}
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filter=array(), $admin=true ) 
	{
		$query  = "SELECT r.*";
		$query .= " FROM $this->_tbl AS r";
		if ($admin) {
			if (isset($filter['search'])) {
				$query .= "\n WHERE (r.title LIKE '%".$filter['search']."%'";
				$query .= "\n OR r.author LIKE '%".$filter['search']."%'";
				if (is_numeric($filter['search'])) {
					$query .= "\n OR r.id=".$filter['search'];
				}
				$query .= ")";
			}
		} else {
			$query .= " WHERE r.id!=''";
			if (isset($filter['type']) && $filter['type']!= '') {
				$query .= " AND r.type='".$filter['type']."'";
			}
			if (isset($filter['filter'])) {
				switch ($filter['filter'])
				{
					case 'aff':
						$query .= " AND affiliated=1";
						break;
					case 'nonaff':
						$query .= " AND affiliated=0";
						break;
					default:
						$query .= "";
						break;
				}
			}
			if (isset($filter['year']) && is_numeric($filter['year']) && $filter['year'] > 0) {
				$query .= " AND r.year='".$filter['year']."'";
			}
			if (isset($filter['search']) && $filter['search']!='') {
				$query .= ($filter['search']) ? " AND (LOWER(r.title) LIKE '%".strtolower($filter['search'])."%' OR LOWER(r.journal) LIKE '%".strtolower($filter['search'])."%')" : "";
			}
		}
		$query .= " ORDER BY ".$filter['sort'];
		if (isset($filter['limit']) && $filter['limit'] > 0) {
			$query .= " LIMIT ".$filter['start'].",".$filter['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function getStats() 
	{
		$stats = array();
		
		for ($i=date("Y"), $n=1998; $i > $n; $i--) 
		{
			$stats[$i] = array();
			
			$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE year='".$i."' AND affiliated=1" );
			$stats[$i]['affiliate'] = $this->_db->loadResult();
			
			$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE year='".$i."' AND affiliated=0" );
			$stats[$i]['non-affiliate'] = $this->_db->loadResult();
		}
		
		return $stats;
	}
	
	//-----------
	
	function getCitations( $tbl, $oid )
	{
		$ca = new CitationsAssociation( $this->_db );
		
		$sql = "SELECT c.*"
			 . "\n FROM $this->_tbl AS c, $ca->_tbl AS a"
			 . "\n WHERE a.table='".$tbl."' AND a.oid='".$oid."' AND a.cid=c.id"
			 . "\n ORDER BY affiliated ASC, year DESC";
	
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function getLastCitationDate( $tbl, $oid )
	{
		$ca = new CitationsAssociation( $this->_db );
		
		$sql = "SELECT c.created "
			 . "\n FROM $this->_tbl AS c, $ca->_tbl AS a"
			 . "\n WHERE a.table='".$tbl."' AND a.oid='".$oid."' AND a.cid=c.id"
			 . "\n ORDER BY created DESC LIMIT 1";
	
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
}


class CitationsAssociation extends JTable 
{
	var $id    = NULL;  // @var int(11) Primary key
	var $cid   = NULL;  // @var int(11)
	var $oid   = NULL;  // @var varchar(200)
	var $type  = NULL;  // @var int(3)
	var $table = NULL;  // @var int(3)
	
	//-----------
	
	function __construct( &$db )
	{
		parent::__construct( '#__citations_assoc', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->cid ) == '') {
			$this->_error = JText::_('ASSOCIATION_MUST_HAVE_CITATION_ID');
			return false;
		}
		if (trim( $this->oid ) == '') {
			$this->_error = JText::_('ASSOCIATION_MUST_HAVE_OBJECT_ID');
			return false;
		}
		return true;
	}
	
	//-----------
	
	function buildQuery( $filters ) 
	{
		$query = "";
		
		if (isset($filters['cid']) && $filters['cid'] != 0) {
			$ands[] = "r.cid='".$filters['cid']."'";
		}
		if (isset($filters['oid']) && $filters['oid'] != 0) {
			$ands[] = "r.oid='".$filters['oid']."'";
		}
		if (isset($filters['type']) && $filters['type'] != '') {
			$ands[] = "r.type='".$filters['type']."'";
		}
		if (isset($filters['type']) && $filters['type'] != '') {
			$ands[] = "r.type='".$filters['type']."'";
		}
		if (isset($filters['table']) && $filters['table'] != '') {
			$ands[] = "r.table='".$filters['table']."'";
		}
		if (count($ands) > 0) {
			$query .= " WHERE ";
			$query .= implode(" AND ", $ands);
		}
		if (isset($filters['sort']) && $filters['sort'] != '') {
			$query .= " ORDER BY ".$filters['sort'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		return $query;
	}
	
	//-----------
	
	function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl AS r";
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filters=array() ) 
	{
		$query  = "SELECT * FROM $this->_tbl AS r";
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}


class CitationsAuthor extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $cid        = NULL;  // @var int(11)
	var $author     = NULL;  // @var varchar(64)
	var $author_uid = NULL;  // @var int(20)
	var $ordering   = NULL;  // @var int(11)
	
	//-----------
	
	function __construct( &$db )
	{
		parent::__construct( '#__citations_authors', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->cid ) == '') {
			$this->_error = JText::_('AUTHOR_MUST_HAVE_CITATION_ID');
			return false;
		}
		if (trim( $this->author ) == '') {
			$this->_error = JText::_('AUTHOR_MUST_HAVE_TEXT');
			return false;
		}
		return true;
	}
	
	//-----------
	
	function buildQuery( $filters ) 
	{
		$query = "";
		
		if (isset($filters['cid']) && $filters['cid'] != 0) {
			$ands[] = "r.cid='".$filters['cid']."'";
		}
		if (isset($filters['author_uid']) && $filters['author_uid'] != 0) {
			$ands[] = "r.author_uid='".$filters['author_uid']."'";
		}
		if (isset($filters['author']) && trim($filters['author']) != '') {
			$ands[] = "LOWER(r.author)='".strtolower($filters['author'])."'";
		}
		if (count($ands) > 0) {
			$query .= " WHERE ";
			$query .= implode(" AND ", $ands);
		}
		if (isset($filters['sort']) && $filters['sort'] != '') {
			$query .= " ORDER BY ".$filters['sort'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		return $query;
	}
	
	//-----------
	
	function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl AS r";
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filters=array() ) 
	{
		$query  = "SELECT * FROM $this->_tbl AS r";
		$query .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
?>