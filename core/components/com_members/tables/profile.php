<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Tables;

use Event;
use Lang;

/**
 * Members table class for profile
 */
class Profile extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct($db)
	{
		parent::__construct('#__xprofiles', 'uidNumber', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->givenName = trim($this->givenName);
		if ($this->givenName == '')
		{
			$this->setError(Lang::txt('MEMBER_MUST_HAVE_FIRST_NAME'));
			return false;
		}

		$this->surname = trim($this->surname);
		if ($this->surname == '')
		{
			$this->setError(Lang::txt('MEMBER_MUST_HAVE_LAST_NAME'));
			return false;
		}

		return true;
	}

	/**
	 * Load an entry from the database and bind to $this
	 *
	 * @param   string   $oid  Username
	 * @return  boolean  True if data was retrieved and loaded
	 */
	public function loadByUsername($oid=NULL)
	{
		return parent::load(array(
			'username' => (string) $oid
		));
	}

	/**
	 * Clears all terms of use agreements
	 *
	 * @return  bool
	 **/
	public function clearTerms()
	{
		$query  = "UPDATE " . $this->_db->quoteName($this->_tbl) . " SET `usageAgreement` = 0";

		$this->_db->setQuery($query);

		return $this->_db->query();
	}

	/**
	 * Construct a query from filters
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @param   boolean  $admin    Admin access?
	 * @return  string   SQL
	 */
	public function buildQuery($filters=array(), $admin)
	{
		// Trigger the functions that return the areas we'll be using
		$select = "";
		if (!isset($filters['count']) && isset($filters['contributions']))
		{
			$bits = Event::trigger('members.onMembersContributionsCount', array($filters['authorized']));
			$select .= ', COALESCE(cv.total_count, 0) AS rcount, COALESCE(cv.resource_count, 0) AS resource_count, COALESCE(cv.wiki_count, 0) AS wiki_count ';
		}

		// Build the query
		$sqlsearch = "";
		if (isset($filters['index']) && $filters['index'] != '')
		{
			$sqlsearch = " ((LEFT(m.surname, 1) = " . $this->_db->quote($filters['index']) . ") OR (LEFT(SUBSTRING_INDEX(m.name, ' ', -1), 1) = " . $this->_db->quote($filters['index'] . '%') . ")) ";
		}

		if (isset($filters['contributions']))
		{
			$sqlsearch .= ($sqlsearch ? ' AND ' : ' ') . 'cv.resource_count + cv.wiki_count >= '. $this->_db->quote($filters['contributions']);
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$words = explode(' ', $filters['search']);
			$words = array_map('strtolower', $words);
			if ($sqlsearch)
			{
				$sqlsearch .= " AND";
			}
			if (!isset($filters['search_field']))
			{
				$filters['search_field'] = 'name';
			}
			switch ($filters['search_field'])
			{
				case 'email':
					$sqlsearch .= " m.email=" . $this->_db->quote($filters['search']) . " ";
				break;

				case 'uidNumber':
					$sqlsearch .= " m.uidNumber=" . $this->_db->quote($filters['search']) . " ";
				break;

				case 'username':
					$sqlsearch .= " m.username=" . $this->_db->quote($filters['search']) . " ";
				break;

				case 'giveName':
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= " (LOWER(m.givenName) LIKE " . $this->_db->quote('%' . $word . '%') . ") OR";
					}
					$sqlsearch = substr($sqlsearch, 0, -3);
					$sqlsearch .= ") ";
				break;

				case 'surname':
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= " (LOWER(m.surname) LIKE " . $this->_db->quote('%' . $word . '%') . ") OR";
					}
					$sqlsearch = substr($sqlsearch, 0, -3);
					$sqlsearch .= ") ";
				break;

				case 'name':
				default:
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= " MATCH (m.name) AGAINST (" . $this->_db->quote($word) . " IN BOOLEAN MODE) OR";
					}
					$sqlsearch = substr($sqlsearch, 0, -3);
					$sqlsearch .= ") ";
				break;
			}
		}

		if (isset($filters['sortby']) && $filters['sortby'] == "RAND()")
		{
			$select .= ", b.bio ";
		}

		$query  = $select."FROM $this->_tbl AS m ";
		if (isset($filters['contributions']))
		{
			$query .= ($filters['show'] == 'contributors' ? 'INNER' : 'LEFT') . ' JOIN #__contributors_view AS cv ON m.uidNumber = cv.uidNumber';
		}

		if (isset($filters['sortby']) && $filters['sortby'] == "RAND()")
		{
			$query .= " LEFT JOIN #__xprofiles_bio AS b ON b.uidNumber=m.uidNumber";
		}
		if ($sqlsearch)
		{
			$query .= ' WHERE' . $sqlsearch;
			if (!$admin || $filters['show'] == 'contributors' || (isset($filters['sortby']) && $filters['sortby'] == "RAND()"))
			{
				$query .= " AND m.public=1";
			}
			if (isset($filters['sortby']) && $filters['sortby'] == "RAND()")
			{
				$query .= " AND b.bio != '' AND b.bio IS NOT NULL AND m.picture != '' AND m.picture IS NOT NULL";
			}
			if ($filters['show'] == 'vips')
			{
				$query .= " AND m.vip=1";
			}
		}
		else
		{
			if (!$admin || $filters['show'] == 'contributors' || (isset($filters['sortby']) && $filters['sortby'] == "RAND()"))
			{
				$query .= " WHERE m.public=1";
				if ($filters['show'] == 'vips')
				{
					$query .= " AND m.vip=1";
				}
			}
			else
			{
				if ($filters['show'] == 'vips')
				{
					$query .= " WHERE m.vip=1";
				}
			}
		}

		if (isset($filters['emailConfirmed']) && $filters['emailConfirmed'] == 1)
		{
			$query .= (strpos($query, 'WHERE') === false) ? " WHERE" : " AND";
			$query .= " m.emailConfirmed >= " . $this->_db->quote($filters['emailConfirmed']);
		}

		return $query;
	}

	/**
	 * Get a record count based off of filters passed
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @param   boolean  $admin    Admin access?
	 * @return  integer
	 */
	public function getCount($filters=array(), $admin=false)
	{
		$filters['count'] = true;
		if ($admin)
		{
			$filters['authorized'] = true;
		}
		$query  = "SELECT count(DISTINCT m.uidNumber) ";
		$query .= $this->buildQuery($filters, $admin);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records based off of filters passed
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @param   boolean  $admin    Admin access?
	 * @return  array
	 */
	public function getRecords($filters=array(), $admin=false)
	{
		if ($admin)
		{
			$filters['authorized'] = true;
		}

		if ($filters['sortby'] == 'fullname ASC')
		{
			$filters['sortby'] = 'lname ASC, fname ASC';
		}

		$query  = "SELECT m.uidNumber, m.username, m.name, m.givenName, m.givenName AS fname, m.middleName, m.middleName AS mname, m.surname, m.surname AS lname, m.organization, m.email, m.vip, m.public, m.picture, m.emailConfirmed, NULL AS lastvisitDate ";
		$query .= $this->buildQuery($filters, $admin);
		$query .= " GROUP BY m.uidNumber";
		if (isset($filters['sortby']) && $filters['sortby'] != '')
		{
			$query .= " ORDER BY ";

			if ($filters['sortby'] == 'contributions')
			{
				if (!isset($filters['contributions']))
				{
					$filters['sortby'] = '';
				}
			}

			switch ($filters['sortby'])
			{
				case 'organization':
					$query .= "organization ASC";
				break;
				case 'contributions':
					$query .= "rcount DESC";
				break;
				case 'name':
					$query .= "lname ASC, fname ASC";
				break;
				case 'RAND()':
					$query .= "RAND()";
				break;
				default:
					if (!$filters['sortby'])
					{
						$query .= "lname ASC, fname ASC";
					}
					else
					{
						$query .= $filters['sortby'] . ", fname ASC";
					}
				break;
			}
		}
		if (isset($filters['limit']) && $filters['limit'] && strtolower($filters['limit']) != 'all')
		{
			$query .= " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Return records for specified fields and filters
	 *
	 * @param   string  $select  Fields to return
	 * @param   string  $where   Filters
	 * @return  array
	 */
	public function selectWhere($select, $where)
	{
		$query = "SELECT $select FROM $this->_tbl WHERE $where";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Construct a query from filters
	 * Use by admin interface
	 *
	 * @param   array   $filters  Filters to construct query from
	 * @return  string  SQL
	 */
	private function _buildQuery($filters=array())
	{
		$where = array();

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$words = explode(' ', $filters['search']);

			$search = array();
			foreach ($words as $word)
			{
				$word = strtolower($word);

				$search[] = "(m.uidNumber=" . $this->_db->quote($word) . ")";
				$search[] = "(LOWER(m.email) LIKE " . $this->_db->quote('%' . $word . '%') . ")";
				$search[] = "(LOWER(m.username) LIKE " . $this->_db->quote('%' . $word . '%') . ")";
				$search[] = "(LOWER(m.givenName) LIKE " . $this->_db->quote('%' . $word . '%') . ")";
				$search[] = "(LOWER(m.surname) LIKE " . $this->_db->quote('%' . $word . '%') . ")";
				$search[] = "(MATCH (m.name) AGAINST (" . $this->_db->quote($word) . " IN BOOLEAN MODE))";
			}

			$where[] = "(" . implode(" OR ", $search) . ")";
		}

		if (isset($filters['public']) && $filters['public'] >= 0)
		{
			$where[] = "m.`public`=" . $this->_db->quote($filters['public']);
		}
		if (isset($filters['emailConfirmed']) && $filters['emailConfirmed'] != 0)
		{
			if ($filters['emailConfirmed'] == 1)
			{
				$where[] = "m.`emailConfirmed` >= " . $this->_db->quote($filters['emailConfirmed']);
			}
			else
			{
				$where[] = "m.`emailConfirmed` < 0";
			}
		}
		if (isset($filters['registerDate']) && $filters['registerDate'] != '')
		{
			$where[] = "m.`registerDate`>=" . $this->_db->quote($filters['registerDate']);
		}

		$query  = "FROM $this->_tbl AS m
					LEFT JOIN #__users AS u ON u.id=m.uidNumber ";
		if (count($where) > 0)
		{
			$query .= "WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count based off of filters passed
	 * Use by admin interface
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getRecordCount($filters=array())
	{
		$query  = "SELECT count(DISTINCT m.uidNumber) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records based off of filters passed
	 * Use by admin interface
	 *
	 * @param   array  $filters  Filters to construct query from
	 * @return  array
	 */
	public function getRecordEntries($filters=array())
	{
		if ($filters['sortby'] == 'fullname ASC')
		{
			$filters['sortby'] = 'lname ASC, fname ASC';
		}

		$query  = "SELECT m.uidNumber, m.username, m.name, m.givenName, m.middleName, m.surname,
						m.organization, m.email, m.emailConfirmed, u.lastvisitDate, m.registerDate, m.picture, m.public,
					CASE WHEN m.givenName IS NOT NULL AND m.givenName != '' AND m.givenName != '&nbsp;' THEN
						m.givenName
					ELSE
						SUBSTRING_INDEX(m.name, ' ', 1)
					END AS fname,
					CASE WHEN m.middleName IS NOT NULL AND m.middleName != '' AND m.middleName != '&nbsp;' THEN
						m.middleName
					ELSE
						SUBSTRING_INDEX(SUBSTRING_INDEX(m.name,' ', 2), ' ',-1)
					END AS mname,
					CASE WHEN m.surname IS NOT NULL AND m.surname != '' AND m.surname != '&nbsp;' THEN
						m.surname
					ELSE
						SUBSTRING_INDEX(m.name, ' ', -1)
					END AS lname ";
		$query .= $this->_buildQuery($filters);

		if (isset($filters['sort']) && $filters['sort'] != '')
		{
			$query .= " ORDER BY " . $filters['sort'] . ' ' . $filters['sort_Dir'];
		}
		else if (isset($filters['sortby']) && $filters['sortby'] != '')
		{
			$query .= " ORDER BY ";
			switch ($filters['sortby'])
			{
				case 'organization':
					$query .= "organization ASC";
				break;
				case 'name':
				default:
					$query .= "lname ASC, fname ASC";
				break;
			}
		}
		if (isset($filters['limit']) && $filters['limit'] && $filters['limit'] != 'all')
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

