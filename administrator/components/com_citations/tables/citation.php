<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Citations\Tables;

use Hubzero\Geocode\Geocode;
use Exception;

/**
 * Citations class
 */
class Citation extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__citations', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, false if not
	 */
	public function check()
	{
		if (trim($this->title) == '')
		{
			$this->setError(\JText::_('COM_CITATIONS_CITATION_MUST_HAVE_TITLE'));
		}
		if ($this->type == '' || !is_numeric($this->type))
		{
			$this->setError(\JText::_('COM_CITATIONS_CITATION_MUST_HAVE_TYPE'));
		}

		if ($this->getError())
		{
			return false;
		}
		return true;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filter  Filters to apply
	 * @param   boolean  $admin   User has admin access
	 * @return  integer
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
						LEFT JOIN `#__users` AS u ON u.id = r.uid
						LEFT JOIN `#__citations_secondary` as CS ON r.id=CS.cid
						JOIN `#__tags_object` as tago ON tago.objectid=r.id
						JOIN `#__tags` as tag ON tag.id=tago.tagid";
		}
		else
		{
			$query  = "SELECT COUNT(*)
						FROM $this->_tbl AS r
						LEFT JOIN `#__users` AS u ON u.id = r.uid
						LEFT JOIN `#__citations_secondary` as CS ON r.id=CS.cid";
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
	 * Build query
	 *
	 * @param   array    $filter  Filters to apply
	 * @param   boolean  $admin   User has admin access
	 * @return  string
	 */
	public function buildQuery($filter=array(), $admin=true)
	{
		if (!isset($filter['published']))
		{
			$filter['published'] = array(1);
		}

		$query = " WHERE r.published IN (" . implode(',', $filter['published']) . ")";

		//search term match
		if (isset($filter['search']) && $filter['search'] != '')
		{
			$query .= " AND (MATCH(r.title, r.isbn, r.doi, r.abstract, r.author, r.publisher) AGAINST (" . $this->_db->quote($filter['search']) . " IN BOOLEAN MODE) > 0)";

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
			$query .= " AND r.author LIKE " . $this->_db->quote('%' . $filter['author'] . '%');
		}

		//published in filter
		if (isset($filter['publishedin']) && $filter['publishedin'] != '')
		{
			$query .= " AND (r.booktitle LIKE " . $this->_db->quote('%' . $filter['publishedin'] . '%') . " OR r.journal LIKE " . $this->_db->quote('%' . $filter['publishedin'] . '%') . ")";
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
			// make sure its valid
			if (!is_array($filter['reftype']))
			{
				throw new Exception(\JText::_('Citations: Invalid search param "reftype"'), 500);
			}

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
					$countries = Geocode::getCountriesByContinent('na');
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
					$countries = Geocode::getCountriesByContinent('eu');
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
					$countries = Geocode::getCountriesByContinent('as');
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

		if (isset($filter['id']) && $filter['id'] > 0)
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
			$query .= " ORDER BY MATCH(r.title, r.isbn, r.doi, r.abstract, r.author, r.publisher) AGAINST (" . $this->_db->quote($filter['search']) . " IN BOOLEAN MODE) DESC";
		}

		// scope & scope Id
		if (isset($filter['scope']) && $filter['scope'] != '')
		{
			$query .= " AND r.scope=" . $this->_db->quote($filter['scope']);
		}
		else
		{
			$query .= " AND r.scope is NULL OR r.scope=''";
		}

		if (isset($filter['scope_id']) && $filter['scope_id'] != NULL)
		{
			$query .= " AND r.scope_id=". $this->_db->quote($filter['scope_id']);
		}
		else
		{
			$query .= " AND r.scope_id is NULL OR r.scope_id=''";
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
	 * Get a list of records
	 *
	 * @param   array    $filter  Filters to apply
	 * @param   boolean  $admin   User has admin access
	 * @return  array
	 */
	public function getRecords($filter=array(), $admin=true)
	{
		if (isset($filter['tag']) && $filter['tag'] != '')
		{
			$query  = "SELECT DISTINCT r.*, CS.sec_cits_cnt AS sec_cnt, CS.search_string, CS.link1_title, CS.link1_url, CS.link2_title, CS.link2_url, CS.link3_title, CS.link3_url, u.username, COUNT(DISTINCT tag.tag) AS uniques
						FROM $this->_tbl AS r
						LEFT JOIN `#__users` AS u ON u.id = r.uid
						LEFT JOIN `#__citations_secondary` as CS ON r.id=CS.cid
						JOIN `#__tags_object` as tago ON tago.objectid=r.id
						JOIN `#__tags` as tag ON tag.id=tago.tagid";
		}
		else
		{
			$query  = "SELECT DISTINCT r.*, CS.sec_cits_cnt AS sec_cnt, CS.search_string, CS.link1_title, CS.link1_url, CS.link2_title, CS.link2_url, CS.link3_title, CS.link3_url, u.username
						FROM $this->_tbl AS r
						LEFT JOIN `#__users` AS u ON u.id = r.uid
						LEFT JOIN `#__citations_secondary` as CS ON r.id=CS.cid";
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
	 * Get a lsit of basic stats
	 *
	 * @return  array
	 */
	public function getStats()
	{
		$stats = array();

		for ($i=date("Y"), $n=1998; $i > $n; $i--)
		{
			$stats[$i] = array();

			$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE published=1 AND year=" . $this->_db->Quote($i) . " AND affiliated=1 AND scope != 'group' ");
			$stats[$i]['affiliate'] = $this->_db->loadResult();

			$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE published=1 AND year=" . $this->_db->Quote($i) . " AND affiliated=0 AND scope != 'group'");
			$stats[$i]['non-affiliate'] = $this->_db->loadResult();
		}

		return $stats;
	}

	/**
	 * Get a list of sitations
	 *
	 * @param   string  $tbl
	 * @param   string  $oid
	 * @return  array
	 */
	public function getCitations($tbl, $oid)
	{
		$ca = new Association($this->_db);

		$sql = "SELECT DISTINCT r.*, CS.sec_cits_cnt AS sec_cnt, CS.search_string, u.username
				FROM $this->_tbl AS r
				LEFT JOIN `#__users` AS u ON u.id = r.uid
				LEFT JOIN `#__citations_secondary` as CS ON r.id=CS.cid, $ca->_tbl AS a
				WHERE r.published=1 AND a.tbl=" . $this->_db->Quote($tbl) . " AND a.oid=" . $this->_db->Quote($oid) . " AND a.cid=r.id
				ORDER BY affiliated ASC, year DESC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Load publication citation
	 *
	 * @param   string  $doi
	 * @param   string  $oid
	 * @return  object
	 */
	public function loadPubCitation($doi, $oid)
	{
		$ca  = new Association($this->_db);

		$sql = "SELECT C.* FROM $this->_tbl AS C ";
		$sql.= " JOIN $ca->_tbl AS a ON a.cid=C.id ";
		$sql.= " WHERE C.doi='" . $doi . "' AND a.tbl='publication' AND a.oid=" . $oid;

		$this->_db->setQuery($sql);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Load entry by DOI
	 *
	 * @param   string   $doi
	 * @return  boolean
	 */
	public function loadByDoi($doi)
	{
		$sql = "SELECT C.* FROM $this->_tbl AS C ";
		$sql.= " WHERE C.doi='" . $doi . "' LIMIT 1";

		$this->_db->setQuery($sql);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get the earliest year we have citations for
	 *
	 * @return  integer
	 */
	public function getEarliestYear()
	{
		//
		$query = "SELECT c.year FROM " . $this->_tbl . " as c WHERE c.published=1 AND c.year <> 0 AND c.year IS NOT NULL ORDER BY c.year ASC LIMIT 1";
		$this->_db->setQuery( $query );
		$earliest_year = $this->_db->loadResult();
		$earliest_year = ($earliest_year) ? $earliest_year : 1990;

		return $earliest_year;
	}

	/**
	 * Get the last citation date
	 *
	 * @param   string   $tbl
	 * @param   string   $oid
	 * @return  itneger
	 */
	public function getLastCitationDate($tbl, $oid)
	{
		$ca = new Association($this->_db);

		$sql = "SELECT c.created "
			 . " FROM $this->_tbl AS c, $ca->_tbl AS a"
			 . " WHERE c.published=1 AND a.tbl=" . $this->_db->Quote($tbl) . " AND a.oid=" . $this->_db->Quote($oid) . " AND a.cid=c.id"
			 . " ORDER BY created DESC LIMIT 1";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}

