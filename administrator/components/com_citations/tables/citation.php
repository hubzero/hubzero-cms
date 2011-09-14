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

class CitationsCitation extends JTable
{
	var $id             	= NULL;  // @var int(11) Primary key
	var $uid            	= NULL;  // @var varchar(200)
	var $affiliated     	= NULL;  // @var int(3)
	var $fundedby       	= NULL;  // @var int(3)
	var $created        	= NULL;  // @var datetime
	var $address        	= NULL;  // @var varchar(250)
	var $author         	= NULL;  // @var varchar(250)
	var $booktitle      	= NULL;  // @var varchar(250)
	var $chapter        	= NULL;  // @var varchar(250)
	var $cite           	= NULL;  // @var varchar(250)
	var $edition        	= NULL;  // @var varchar(250)
	var $editor         	= NULL;  // @var varchar(250)
	var $eprint         	= NULL;  // @var varchar(250)
	var $howpublished   	= NULL;  // @var varchar(250)
	var $institution    	= NULL;  // @var varchar(250)
	var $isbn           	= NULL;  // @var varchar(50)
	var $journal        	= NULL;  // @var varchar(250)
	var $key            	= NULL;  // @var varchar(250)
	var $location       	= NULL;  // @var varchar(250)
	var $month          	= NULL;  // @var int(2)
	var $note           	= NULL;  // @var text
	var $number         	= NULL;  // @var int(11)
	var $organization   	= NULL;  // @var varchar(250)
	var $pages          	= NULL;  // @var varchar(250)
	var $publisher      	= NULL;  // @var varchar(250)
	var $school         	= NULL;  // @var varchar(250)
	var $series         	= NULL;  // @var varchar(250)
	var $title          	= NULL;  // @var varchar(250)
	var $type           	= NULL;  // @var varchar(250)
	var $url            	= NULL;  // @var varchar(250)
	var $volume         	= NULL;  // @var int(11)
	var $year           	= NULL;  // @var int(4)
	var $doi            	= NULL;  // @var varchar(50)
	var $ref_type       	= NULL;  // @var varchar(50)
	var $date_submit    	= NULL;  // @var datetime(0000-00-00 00:00:00)
	var $date_accept    	= NULL;  // @var datetime(0000-00-00 00:00:00)
	var $date_publish   	= NULL;  // @var datetime(0000-00-00 00:00:00)
	var $software_use   	= NULL;  // @var int(3)
    var	$res_edu 			= NULL;  // @var int(3)
	var $exp_list_exp_data  = NULL;  // @var int(3)
 	var $exp_data       	= NULL;  // @var int(3)
 	var $notes          	= NULL;  // @var text
	var $published      	= NULL;  // @var int(3)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__citations', 'id', $db );
	}

	public function check()
	{
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('CITATION_MUST_HAVE_TITLE') );
			return false;
		}
		return true;
	}

	public function getCount( $filter=array(), $admin=true )
	{
		$filter['sort'] = '';
		$filter['limit'] = 0;

		$query = "SELECT count(DISTINCT r.id) FROM $this->_tbl AS r";
		if (isset($filter['geo']) || isset($filter['aff'])) {
			$q = false;
			if ((isset($filter['geo']['us']) && $filter['geo']['us'] == 1)
			 && (isset($filter['geo']['na']) && $filter['geo']['na'] == 1)
			 && (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1)
			 && (isset($filter['geo']['as']) && $filter['geo']['as'] == 1)) {
				// Show all
			} else {
				$q = true;
			}
			if ((isset($filter['aff']['university']) && $filter['aff']['university'] == 1)
			 && (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1)
			 && (isset($filter['aff']['government']) && $filter['aff']['government'] == 1)) {
				// Show all
			} else {
				$q = true;
			}
			if ($q) {
				$query .= ", #__citations_authors AS ca";
			}
		}
		$query .= $this->buildQuery( $filter, $admin );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	public function buildQuery( $filter=array(), $admin=true )
	{
		$query = "";
		if ($admin) {
			if (isset($filter['search'])) {
				$query .= " WHERE r.published=1 AND (r.title LIKE '%".strtolower($filter['search'])."%'";
				$query .= " OR LOWER(r.author) LIKE '%".strtolower($filter['search'])."%'";
				if (is_numeric($filter['search'])) {
					$query .= " OR r.id=".$filter['search'];
				}
				$query .= ")";
			}
		} else {
			$query .= " WHERE r.published=1 AND r.id!=''";
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
				$query .= ($filter['search'])
						? " AND (LOWER(r.title) LIKE '%".strtolower($filter['search'])."%' 
							OR LOWER(r.journal) LIKE '%".strtolower($filter['search'])."%' 
							OR LOWER(r.author) LIKE '%".strtolower($filter['search'])."%')"
						: "";
			}
			if (isset($filter['reftype'])) {
				if ((isset($filter['reftype']['research']) && $filter['reftype']['research'] == 1)
				 && (isset($filter['reftype']['education']) && $filter['reftype']['education'] == 1)
				 && (isset($filter['reftype']['eduresearch']) && $filter['reftype']['eduresearch'] == 1)
				 && (isset($filter['reftype']['cyberinfrastructure']) && $filter['reftype']['cyberinfrastructure'] == 1)) {
					// Show all
				} else {
					$query .= " AND";
					$multi = 0;
					$o = 0;
					foreach ($filter['reftype'] as $g)
					{
						if ($g == 1) {
							$multi++;
						}
					}
					if ($multi) {
						$query .= " (";
					}
					if (isset($filter['reftype']['research']) && $filter['reftype']['research'] == 1) {
						$query .= " ((ref_type LIKE '%R%' OR ref_type LIKE '%N%' OR ref_type LIKE '%S%') AND ref_type NOT LIKE '%E%')";
						if ($multi) {
							$o = 1;
						}
					}
					if (isset($filter['reftype']['education']) && $filter['reftype']['education'] == 1) {
						if ($multi) {
							$query .= ($o == 1) ? " OR" : "";
							$o = 1;
						}
						$query .= " ((ref_type NOT LIKE '%R%' AND ref_type NOT LIKE '%N%' AND ref_type NOT LIKE '%S%') AND ref_type LIKE '%E%')";
					}
					if (isset($filter['reftype']['eduresearch']) && $filter['reftype']['eduresearch'] == 1) {
						if ($multi) {
							$query .= ($o == 1) ? " OR" : "";
							$o = 1;
						}
						$query .= " (ref_type LIKE '%R%E%' OR ref_type LIKE '%E%R%' AND ref_type LIKE '%N%E%' OR ref_type LIKE '%E%N%' OR ref_type LIKE '%S%E%' OR ref_type LIKE '%E%S%')";
					}
					if (isset($filter['reftype']['cyberinfrastructure']) && $filter['reftype']['cyberinfrastructure'] == 1) {
						if ($multi) {
							$query .= ($o == 1) ? " OR" : "";
							$o = 1;
						}
						$query .= " ((ref_type LIKE '%C%' OR ref_type LIKE '%A%' OR ref_type LIKE '%HD%' OR ref_type LIKE '%I%') AND (ref_type NOT LIKE '%R%' AND ref_type NOT LIKE '%N%' AND ref_type NOT LIKE '%S%' AND ref_type NOT LIKE '%E%'))";
					}
					if ($multi) {
						$query .= " )";
					}
				}
			}
			if (isset($filter['aff'])) {
				if ((isset($filter['aff']['university']) && $filter['aff']['university'] == 1)
				 && (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1)
				 && (isset($filter['aff']['government']) && $filter['aff']['government'] == 1)) {
					// Show all
				} else {
					$query .= " AND ca.cid=r.id AND";
					$multi = 0;
					$o = 0;
					foreach ($filter['aff'] as $g)
					{
						if ($g == 1) {
							$multi++;
						}
					}
					if ($multi) {
						$query .= " (";
					}
					if (isset($filter['aff']['university']) && $filter['aff']['university'] == 1) {
						$query .= " (ca.orgtype LIKE '%education%' OR ca.orgtype LIKE 'university%')";
						if ($multi) {
							$o = 1;
						}
					}
					if (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1) {
						if ($multi) {
							$query .= ($o == 1) ? " OR" : "";
							$o = 1;
						}
						$query .= " ca.orgtype LIKE '%industry%'";
					}
					if (isset($filter['aff']['government']) && $filter['aff']['government'] == 1) {
						if ($multi) {
							$query .= ($o == 1) ? " OR" : "";
							$o = 1;
						}
						$query .= " ca.orgtype LIKE '%government%'";
					}
					if ($multi) {
						$query .= " )";
					}
				}
			}
			if (isset($filter['geo'])) {
				if ((isset($filter['geo']['us']) && $filter['geo']['us'] == 1)
				 && (isset($filter['geo']['na']) && $filter['geo']['na'] == 1)
				 && (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1)
				 && (isset($filter['geo']['as']) && $filter['geo']['as'] == 1)) {
					// Show all
				} else {
					ximport('Hubzero_Geo');

					$query .= " AND ca.cid=r.id AND";

					$multi = 0;
					$o = 0;
					foreach ($filter['geo'] as $g)
					{
						if ($g == 1) {
							$multi++;
						}
					}
					if ($multi) {
						$query .= " (";
					}
					if (isset($filter['geo']['us']) && $filter['geo']['us'] == 1) {
						$query .= " LOWER(ca.countryresident) = 'us'";
						if ($multi) {
							$o = 1;
						}
					}
					if (isset($filter['geo']['na']) && $filter['geo']['na'] == 1) {
						$countries = Hubzero_Geo::getCountriesByContinent('na');
						$c = implode("','",$countries);
						if ($multi) {
							$query .= ($o == 1) ? " OR" : "";
							$o = 1;
						}
						$query .= " LOWER(ca.countryresident) IN ('".strtolower($c)."')";
					}
					if (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1) {
						$countries = Hubzero_Geo::getCountriesByContinent('eu');
						$c = implode("','",$countries);
						if ($multi) {
							$query .= ($o == 1) ? " OR" : "";
							$o = 1;
						}
						$query .= " LOWER(ca.countryresident) IN ('".strtolower($c)."')";
					}
					if (isset($filter['geo']['as']) && $filter['geo']['as'] == 1) {
						$countries = Hubzero_Geo::getCountriesByContinent('as');
						$c = implode("','",$countries);
						if ($multi) {
							$query .= ($o == 1) ? " OR" : "";
							$o = 1;
						}
						$query .= " LOWER(ca.countryresident) IN ('".strtolower($c)."')";
					}
					if ($multi) {
						$query .= " )";
					}
				}
			}
		}
		if (isset($filter['sort']) && $filter['sort'] != '') {
			$query .= " ORDER BY ".$filter['sort'];
		}
		if (isset($filter['limit']) && $filter['limit'] > 0) {
			$query .= " LIMIT ".$filter['start'].",".$filter['limit'];
		}
		return $query;
	}

	public function getRecords( $filter=array(), $admin=true )
	{
		$query  = "SELECT DISTINCT r.*, CS.sec_cits_cnt AS sec_cnt, CS.search_string 
					FROM $this->_tbl AS r 
					LEFT JOIN #__citations_secondary as CS ON r.id=CS.cid";
		if (isset($filter['geo']) || isset($filter['aff'])) {
			$q = false;
			if ((isset($filter['geo']['us']) && $filter['geo']['us'] == 1)
			 && (isset($filter['geo']['na']) && $filter['geo']['na'] == 1)
			 && (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1)
			 && (isset($filter['geo']['as']) && $filter['geo']['as'] == 1)) {
				// Show all
			} else {
				$q = true;
			}
			if ((isset($filter['aff']['university']) && $filter['aff']['university'] == 1)
			 && (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1)
			 && (isset($filter['aff']['government']) && $filter['aff']['government'] == 1)) {
				// Show all
			} else {
				$q = true;
			}
			if ($q) {
				$query .= ", #__citations_authors AS ca";
			}
		}
		$query .= $this->buildQuery( $filter, $admin );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	public function getStats()
	{
		$stats = array();

		for ($i=date("Y"), $n=1998; $i > $n; $i--)
		{
			$stats[$i] = array();

			$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE published=1 AND year='".$i."' AND affiliated=1" );
			$stats[$i]['affiliate'] = $this->_db->loadResult();

			$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE published=1 AND year='".$i."' AND affiliated=0" );
			$stats[$i]['non-affiliate'] = $this->_db->loadResult();
		}

		return $stats;
	}

	public function getCitations( $tbl, $oid )
	{
		$ca = new CitationsAssociation( $this->_db );

		$sql = "SELECT DISTINCT c.*, CS.sec_cits_cnt AS sec_cnt, CS.search_string 
				FROM $this->_tbl AS c 
				LEFT JOIN #__citations_secondary as CS ON c.id=CS.cid, $ca->_tbl AS a 
				WHERE c.published=1 AND a.table='".$tbl."' AND a.oid='".$oid."' AND a.cid=c.id 
				ORDER BY affiliated ASC, year DESC";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	public function getLastCitationDate( $tbl, $oid )
	{
		$ca = new CitationsAssociation( $this->_db );

		$sql = "SELECT c.created "
			 . "\n FROM $this->_tbl AS c, $ca->_tbl AS a"
			 . "\n WHERE c.published=1 AND a.table='".$tbl."' AND a.oid='".$oid."' AND a.cid=c.id"
			 . "\n ORDER BY created DESC LIMIT 1";

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
}

