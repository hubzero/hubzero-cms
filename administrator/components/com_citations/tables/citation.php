<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Citations class
 */
class CitationsCitation extends JTable
{
	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id             	= NULL;  // @var int(11) Primary key

	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid            	= NULL;  // @var varchar(200)

	/**
	 * Description for 'affiliated'
	 * 
	 * @var unknown
	 */
	var $affiliated     	= NULL;  // @var int(3)

	/**
	 * Description for 'fundedby'
	 * 
	 * @var unknown
	 */
	var $fundedby       	= NULL;  // @var int(3)

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created        	= NULL;  // @var datetime

	/**
	 * Description for 'address'
	 * 
	 * @var unknown
	 */
	var $address        	= NULL;  // @var varchar(250)

	/**
	 * Description for 'author'
	 * 
	 * @var unknown
	 */
	var $author         	= NULL;  // @var varchar(250)

	/**
	 * Description for 'booktitle'
	 * 
	 * @var unknown
	 */
	var $booktitle      	= NULL;  // @var varchar(250)

	/**
	 * Description for 'chapter'
	 * 
	 * @var unknown
	 */
	var $chapter        	= NULL;  // @var varchar(250)

	/**
	 * Description for 'cite'
	 * 
	 * @var unknown
	 */
	var $cite           	= NULL;  // @var varchar(250)

	/**
	 * Description for 'edition'
	 * 
	 * @var unknown
	 */
	var $edition        	= NULL;  // @var varchar(250)

	/**
	 * Description for 'editor'
	 * 
	 * @var unknown
	 */
	var $editor         	= NULL;  // @var varchar(250)

	/**
	 * Description for 'eprint'
	 * 
	 * @var unknown
	 */
	var $eprint         	= NULL;  // @var varchar(250)

	/**
	 * Description for 'howpublished'
	 * 
	 * @var unknown
	 */
	var $howpublished   	= NULL;  // @var varchar(250)

	/**
	 * Description for 'institution'
	 * 
	 * @var unknown
	 */
	var $institution    	= NULL;  // @var varchar(250)

	/**
	 * Description for 'isbn'
	 * 
	 * @var unknown
	 */
	var $isbn           	= NULL;  // @var varchar(50)

	/**
	 * Description for 'journal'
	 * 
	 * @var unknown
	 */
	var $journal        	= NULL;  // @var varchar(250)

	/**
	 * Description for 'key'
	 * 
	 * @var unknown
	 */
	var $key            	= NULL;  // @var varchar(250)

	/**
	 * Description for 'location'
	 * 
	 * @var unknown
	 */
	var $location       	= NULL;  // @var varchar(250)

	/**
	 * Description for 'month'
	 * 
	 * @var unknown
	 */
	var $month          	= NULL;  // @var int(2)

	/**
	 * Description for 'note'
	 * 
	 * @var unknown
	 */
	var $note           	= NULL;  // @var text

	/**
	 * Description for 'number'
	 * 
	 * @var unknown
	 */
	var $number         	= NULL;  // @var int(11)

	/**
	 * Description for 'organization'
	 * 
	 * @var unknown
	 */
	var $organization   	= NULL;  // @var varchar(250)

	/**
	 * Description for 'pages'
	 * 
	 * @var unknown
	 */
	var $pages          	= NULL;  // @var varchar(250)

	/**
	 * Description for 'publisher'
	 * 
	 * @var unknown
	 */
	var $publisher      	= NULL;  // @var varchar(250)

	/**
	 * Description for 'school'
	 * 
	 * @var unknown
	 */
	var $school         	= NULL;  // @var varchar(250)

	/**
	 * Description for 'series'
	 * 
	 * @var unknown
	 */
	var $series         	= NULL;  // @var varchar(250)

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title          	= NULL;  // @var varchar(250)

	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	var $type           	= NULL;  // @var varchar(250)

	/**
	 * Description for 'url'
	 * 
	 * @var unknown
	 */
	var $url            	= NULL;  // @var varchar(250)

	/**
	 * Description for 'volume'
	 * 
	 * @var unknown
	 */
	var $volume         	= NULL;  // @var int(11)

	/**
	 * Description for 'year'
	 * 
	 * @var unknown
	 */
	var $year           	= NULL;  // @var int(4)

	/**
	 * Description for 'doi'
	 * 
	 * @var unknown
	 */
	var $doi            	= NULL;  // @var varchar(50)

	/**
	 * Description for 'ref_type'
	 * 
	 * @var unknown
	 */
	var $ref_type       	= NULL;  // @var varchar(50)

	/**
	 * Description for 'date_submit'
	 * 
	 * @var unknown
	 */
	var $date_submit    	= NULL;  // @var datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'date_accept'
	 * 
	 * @var unknown
	 */
	var $date_accept    	= NULL;  // @var datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'date_publish'
	 * 
	 * @var unknown
	 */
	var $date_publish   	= NULL;  // @var datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'software_use'
	 * 
	 * @var unknown
	 */
	var $software_use   	= NULL;  // @var int(3)

	/**
	 * Description for 'res_edu'
	 * 
	 * @var unknown
	 */
    var	$res_edu 			= NULL;  // @var int(3)

	/**
	 * Description for 'exp_list_exp_data'
	 * 
	 * @var unknown
	 */
	var $exp_list_exp_data  = NULL;  // @var int(3)

	/**
	 * Description for 'exp_data'
	 * 
	 * @var unknown
	 */
 	var $exp_data       	= NULL;  // @var int(3)

	/**
	 * Description for 'notes'
	 * 
	 * @var unknown
	 */
 	var $notes          	= NULL;  // @var text

	/**
	 * Description for 'published'
	 * 
	 * @var unknown
	 */
	var $published      	= NULL;  // @var int(3)

	/**
	 * Description for 'abstract'
	 * 
	 * @var unknown
	 */
	var $abstract 			= NULL;

	/**
	 * Description for 'keywords'
	 * 
	 * @var unknown
	 */
	var $keywords			= NULL;

	/**
	 * Description for 'language'
	 * 
	 * @var unknown
	 */
	var $language			= NULL;

	/**
	 * Description for 'accession_number'
	 * 
	 * @var unknown
	 */
	var $accession_number	= NULL;

	/**
	 * Description for 'short_title'
	 * 
	 * @var unknown
	 */
	var $short_title 		= NULL;

	/**
	 * Description for 'research_notes'
	 * 
	 * @var unknown
	 */
	var $research_notes		= NULL;

	/**
	 * Description for 'author_address'
	 * 
	 * @var unknown
	 */
	var $author_address 	= NULL;

	/**
	 * Description for 'call_number'
	 * 
	 * @var unknown
	 */
	var $call_number		= NULL;

	/**
	 * Description for 'label'
	 * 
	 * @var unknown
	 */
	var $label 				= NULL;

	/**
	 * Description for 'badges'
	 * 
	 * @var unknown
	 */
	var $badges				= NULL;

	/**
	 * Description for 'tags'
	 * 
	 * @var unknown
	 */
	var $tags				= NULL;
	
	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $params				= NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__citations', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if valid, false if not
	 */
	public function check()
	{
		if (trim($this->title) == '') 
		{
			$this->setError(JText::_('CITATION_MUST_HAVE_TITLE'));
			return false;
		}

		if ($this->type == '' || !is_numeric($this->type))
		{
			$this->setError(JText::_('CITATION_MUST_HAVE_TYPE'));
			return false; 
		}
		return true;
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filter Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount($filter=array(), $admin=true)
	{
		$filter['sort'] = '';
		$filter['limit'] = 0;
		
		if (isset($filter['tag']) && $filter['tag'] != '')
		{
			$query  = "SELECT COUNT(b.id) FROM
			 			(
						SELECT r.id, COUNT(DISTINCT tag.tag) AS uniques
						FROM $this->_tbl AS r 
						LEFT JOIN #__users AS u ON u.id = r.uid
						LEFT JOIN #__citations_secondary as CS ON r.id=CS.cid
						JOIN #__tags_object as tago ON tago.objectid=r.id
						JOIN #__tags as tag ON tag.id=tago.tagid";
		}
		else
		{
			$query  = "SELECT COUNT(*)
						FROM $this->_tbl AS r 
						LEFT JOIN #__users AS u ON u.id = r.uid
						LEFT JOIN #__citations_secondary as CS ON r.id=CS.cid";
		}
		
		if (isset($filter['geo']) || isset($filter['aff'])) 
		{
			$q = false;
			if ((isset($filter['geo']['us']) && $filter['geo']['us'] == 1)
			 && (isset($filter['geo']['na']) && $filter['geo']['na'] == 1)
			 && (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1)
			 && (isset($filter['geo']['as']) && $filter['geo']['as'] == 1)) 
			{
				// Show all
			} 
			else 
			{
				$q = true;
			}
			if ((isset($filter['aff']['university']) && $filter['aff']['university'] == 1)
			 && (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1)
			 && (isset($filter['aff']['government']) && $filter['aff']['government'] == 1)) 
			{
				// Show all
			} 
			else 
			{
				$q = true;
			}
			if ($q) 
			{
				$query .= ", #__citations_authors AS ca";
			}
		}
		
		$query .= $this->buildQuery($filter, $admin);
		
		if (isset($filter['tag']) && $filter['tag'] != '')
		{
			$query .= ") as b";
		}
		
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filter Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filter=array(), $admin=true)
	{
		$query = " WHERE r.published=1";
		
		//search term match
		if (isset($filter['search']) && $filter['search'] != '')
		{
			$query .= " AND (MATCH(r.title, r.isbn, r.doi, r.abstract, r.author, r.publisher) AGAINST ('" . $filter['search'] . "') > 0)";

			//if ($admin = true)
			//{
			//	$query .= " OR LOWER(u.username) = " . $this->_db->Quote(strtolower($filter['search'])) . "
			//				OR r.uid = " . $this->_db->Quote($filter['search']);
			//}
		}

		//tag search
		if (isset($filter['tag']) && $filter['tag'] != '')
		{
			//if we have multiple tags we must explode them
			if (strstr($filter['tag'], ","))
			{
				$tags = array_filter(array_map('trim',explode(',', $filter['tag'])));
			}
			else
			{
				$tags = array($filter['tag']);
			}
			
			$query .= " AND tago.tbl='citations' AND tag.tag IN ('" . implode("','", $tags) . "')";
		}
		
		//type filter
		if (isset($filter['type']) && $filter['type'] != '')
		{
			$query .= " AND r.type=" . $this->_db->Quote($filter['type']);
		}
		
		//author filter
		if (isset($filter['author']) && $filter['author'] != '')
		{
			$query .= " AND r.author LIKE '%" . $this->_db->getEscaped($filter['author']) . "%'";
		}
		
		//published in filter
		if (isset($filter['publishedin']) && $filter['publishedin'] != '')
		{
			$query .= " AND (r.booktitle LIKE '%" . $this->_db->getEscaped($filter['publishedin']) . "%' OR r.journal LIKE '%" . $this->_db->getEscaped($filter['publishedin']) . "%')";
		}
		
		//year filter
		if (isset($filter['year_start']) && is_numeric($filter['year_start']) && $filter['year_start'] > 0)
		{
			$query .= " AND (r.year >=" . $this->_db->Quote($filter['year_start']) . " OR r.year IS NULL OR r.year=0)";
		}
		if (isset($filter['year_end']) && is_numeric($filter['year_end']) && $filter['year_end'] > 0)
		{
			$query .= " AND (r.year <=" . $this->_db->Quote($filter['year_end']) . " OR r.year IS NULL OR r.year=0)";
		}
		if (isset($filter['startuploaddate']) && isset($filter['enduploaddate']))
		{
			$query .= " AND r.created >= " . $this->_db->Quote($filter['startuploaddate']) . " AND r.created <= " . $this->_db->Quote($filter['enduploaddate']);
		}

		//affiated? filter
		if (isset($filter['filter']) && $filter['filter'] != '')
		{
			if ($filter['filter'] == 'aff')
			{
				$query .= " AND r.affiliated=1";
			}
			else
			{
				$query .= " AND r.affiliated=0";
			}
		}
		
		//reference type check
		if (isset($filter['reftype'])) 
		{
			if ((isset($filter['reftype']['research']) && $filter['reftype']['research'] == 1)
			 && (isset($filter['reftype']['education']) && $filter['reftype']['education'] == 1)
			 && (isset($filter['reftype']['eduresearch']) && $filter['reftype']['eduresearch'] == 1)
			 && (isset($filter['reftype']['cyberinfrastructure']) && $filter['reftype']['cyberinfrastructure'] == 1)) 
			{
				// Show all
			} 
			else 
			{
				$query .= " AND";
				$multi = 0;
				$o = 0;
				foreach ($filter['reftype'] as $g)
				{
					if ($g == 1) 
					{
						$multi++;
					}
				}
				if ($multi) 
				{
					$query .= " (";
				}
				if (isset($filter['reftype']['research']) && $filter['reftype']['research'] == 1) 
				{
					$query .= " ((ref_type LIKE '%R%' OR ref_type LIKE '%N%' OR ref_type LIKE '%S%') AND ref_type NOT LIKE '%E%')";
					if ($multi) 
					{
						$o = 1;
					}
				}
				if (isset($filter['reftype']['education']) && $filter['reftype']['education'] == 1) 
				{
					if ($multi) 
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " ((ref_type NOT LIKE '%R%' AND ref_type NOT LIKE '%N%' AND ref_type NOT LIKE '%S%') AND ref_type LIKE '%E%')";
				}
				if (isset($filter['reftype']['eduresearch']) && $filter['reftype']['eduresearch'] == 1) 
				{
					if ($multi) 
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " (ref_type LIKE '%R%E%' OR ref_type LIKE '%E%R%' AND ref_type LIKE '%N%E%' OR ref_type LIKE '%E%N%' OR ref_type LIKE '%S%E%' OR ref_type LIKE '%E%S%')";
				}
				if (isset($filter['reftype']['cyberinfrastructure']) && $filter['reftype']['cyberinfrastructure'] == 1) 
				{
					if ($multi) 
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " ((ref_type LIKE '%C%' OR ref_type LIKE '%A%' OR ref_type LIKE '%HD%' OR ref_type LIKE '%I%') AND (ref_type NOT LIKE '%R%' AND ref_type NOT LIKE '%N%' AND ref_type NOT LIKE '%S%' AND ref_type NOT LIKE '%E%'))";
				}
				if ($multi) 
				{
					$query .= ")";
				}
			}
		}
		
		//author affiliation filter
		if (isset($filter['aff'])) 
		{
			if ((isset($filter['aff']['university']) && $filter['aff']['university'] == 1)
			 && (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1)
			 && (isset($filter['aff']['government']) && $filter['aff']['government'] == 1)) 
			{
				// Show all
			} 
			else 
			{
				$query .= " AND ca.cid=r.id AND";
				$multi = 0;
				$o = 0;
				foreach ($filter['aff'] as $g)
				{
					if ($g == 1) 
					{
						$multi++;
					}
				}
				if ($multi) 
				{
					$query .= " (";
				}
				if (isset($filter['aff']['university']) && $filter['aff']['university'] == 1) 
				{
					$query .= " (ca.orgtype LIKE '%education%' OR ca.orgtype LIKE 'university%')";
					if ($multi) 
					{
						$o = 1;
					}
				}
				if (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1) 
				{
					if ($multi) 
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " ca.orgtype LIKE '%industry%'";
				}
				if (isset($filter['aff']['government']) && $filter['aff']['government'] == 1) 
				{
					if ($multi) 
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " ca.orgtype LIKE '%government%'";
				}
				if ($multi) 
				{
					$query .= ")";
				}
			}
		}
		
		//author geo filter
		if (isset($filter['geo'])) 
		{
			if ((isset($filter['geo']['us']) && $filter['geo']['us'] == 1)
			 && (isset($filter['geo']['na']) && $filter['geo']['na'] == 1)
			 && (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1)
			 && (isset($filter['geo']['as']) && $filter['geo']['as'] == 1)) 
			{
				// Show all
			} 
			else 
			{
				ximport('Hubzero_Geo');

				$query .= " AND ca.cid=r.id AND";

				$multi = 0;
				$o = 0;
				foreach ($filter['geo'] as $g)
				{
					if ($g == 1) 
					{
						$multi++;
					}
				}
				if ($multi) 
				{
					$query .= " (";
				}
				if (isset($filter['geo']['us']) && $filter['geo']['us'] == 1) 
				{
					$query .= " LOWER(ca.countryresident) = 'us'";
					if ($multi) 
					{
						$o = 1;
					}
				}
				if (isset($filter['geo']['na']) && $filter['geo']['na'] == 1) 
				{
					$countries = Hubzero_Geo::getCountriesByContinent('na');
					$c = implode("','",$countries);
					if ($multi) 
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " LOWER(ca.countryresident) IN ('" . strtolower($c) . "')";
				}
				if (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1) 
				{
					$countries = Hubzero_Geo::getCountriesByContinent('eu');
					$c = implode("','", $countries);
					if ($multi) 
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " LOWER(ca.countryresident) IN ('" . strtolower($c) . "')";
				}
				if (isset($filter['geo']['as']) && $filter['geo']['as'] == 1) 
				{
					$countries = Hubzero_Geo::getCountriesByContinent('as');
					$c = implode("','", $countries);
					if ($multi) 
					{
						$query .= ($o == 1) ? " OR" : "";
						$o = 1;
					}
					$query .= " LOWER(ca.countryresident) IN ('" . strtolower($c) . "')";
				}
				if ($multi) 
				{
					$query .= ")";
				}
			}
		}
		
		if(isset($filter['id']) && $filter['id'] > 0)
		{
			$query .= " AND r.id=" . $filter['id'];
		}
		
		//group by
		if (isset($filter['tag']) && $filter['tag'] != '')
		{
			$query .= " GROUP BY r.id HAVING uniques=" . count($tags);
		}

		//if we had a search term lets order by search match
		if (isset($filter['search']) && $filter['search'] != '')
		{
			$query .= " ORDER BY MATCH(r.title, r.isbn, r.doi, r.abstract, r.author, r.publisher) AGAINST ('" . $filter['search'] . "') DESC";
		}

		//sort filter
		if (isset($filter['sort']) && $filter['sort'] != '')
		{
			if (isset($filter['search']) && $filter['search'] != '')
			{
				$query .= ", " . $filter['sort'];
			}
			else
			{
				$query .= " ORDER BY " . $filter['sort'];
			}
		}

		//limit
		if (isset($filter['limit']) && $filter['limit'] > 0)
		{
			$query .= " LIMIT " . intval($filter['start']) . "," . intval($filter['limit']);
		}
		
		return $query;
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filter Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords($filter=array(), $admin=true)
	{
		if (isset($filter['tag']) && $filter['tag'] != '')
		{
			$query  = "SELECT DISTINCT r.*, CS.sec_cits_cnt AS sec_cnt, CS.search_string, u.username, COUNT(DISTINCT tag.tag) AS uniques 
						FROM $this->_tbl AS r 
						LEFT JOIN #__users AS u ON u.id = r.uid
						LEFT JOIN #__citations_secondary as CS ON r.id=CS.cid
						JOIN #__tags_object as tago ON tago.objectid=r.id
						JOIN #__tags as tag ON tag.id=tago.tagid";
		}
		else
		{
			$query  = "SELECT DISTINCT r.*, CS.sec_cits_cnt AS sec_cnt, CS.search_string, u.username 
						FROM $this->_tbl AS r
						LEFT JOIN #__users AS u ON u.id = r.uid
						LEFT JOIN #__citations_secondary as CS ON r.id=CS.cid";
		}

		if (isset($filter['geo']) || isset($filter['aff'])) 
		{
			$q = false;
			if ((isset($filter['geo']['us']) && $filter['geo']['us'] == 1)
			 && (isset($filter['geo']['na']) && $filter['geo']['na'] == 1)
			 && (isset($filter['geo']['eu']) && $filter['geo']['eu'] == 1)
			 && (isset($filter['geo']['as']) && $filter['geo']['as'] == 1)) 
			{
				// Show all
			} 
			else 
			{
				$q = true;
			}
			if ((isset($filter['aff']['university']) && $filter['aff']['university'] == 1)
			 && (isset($filter['aff']['industry']) && $filter['aff']['industry'] == 1)
			 && (isset($filter['aff']['government']) && $filter['aff']['government'] == 1)) 
			{
				// Show all
			} 
			else 
			{
				$q = true;
			}
			if ($q) 
			{
				$query .= ", #__citations_authors AS ca";
			}
		}

		$query .= $this->buildQuery($filter, $admin);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getStats'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function getStats()
	{
		$stats = array();

		for ($i=date("Y"), $n=1998; $i > $n; $i--)
		{
			$stats[$i] = array();

			$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE published=1 AND year=" . $this->_db->Quote($i) . " AND affiliated=1");
			$stats[$i]['affiliate'] = $this->_db->loadResult();

			$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE published=1 AND year=" . $this->_db->Quote($i) . " AND affiliated=0");
			$stats[$i]['non-affiliate'] = $this->_db->loadResult();
		}

		return $stats;
	}

	/**
	 * Short description for 'getCitations'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $tbl Parameter description (if any) ...
	 * @param      string $oid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCitations($tbl, $oid)
	{
		$ca = new CitationsAssociation($this->_db);

		$sql = "SELECT DISTINCT r.*, CS.sec_cits_cnt AS sec_cnt, CS.search_string, u.username 
				FROM $this->_tbl AS r
				LEFT JOIN #__users AS u ON u.id = r.uid
				LEFT JOIN #__citations_secondary as CS ON r.id=CS.cid, $ca->_tbl AS a
				WHERE r.published=1 AND a.tbl=" . $this->_db->Quote($tbl) . " AND a.oid=" . $this->_db->Quote($oid) . " AND a.cid=r.id 
				ORDER BY affiliated ASC, year DESC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getLastCitationDate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $tbl Parameter description (if any) ...
	 * @param      string $oid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getLastCitationDate($tbl, $oid)
	{
		$ca = new CitationsAssociation($this->_db);

		$sql = "SELECT c.created "
			 . " FROM $this->_tbl AS c, $ca->_tbl AS a"
			 . " WHERE c.published=1 AND a.tbl=" . $this->_db->Quote($tbl) . " AND a.oid=" . $this->_db->Quote($oid) . " AND a.cid=c.id"
			 . " ORDER BY created DESC LIMIT 1";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}

