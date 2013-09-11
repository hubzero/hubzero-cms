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
 * Members table class for profile
 */
class MembersProfile extends JTable
{
	/**
	 * in(11)
	 * 
	 * @var integer
	 */
	var $uidNumber = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $name = null;

	/**
	 * varchar(150)
	 * 
	 * @var string
	 */
	var $username = null;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $email = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $registerDate = null;

	/**
	 * varchar(11)
	 * 
	 * @var string
	 */
	var $gidNumber = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $homeDirectory = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $loginShell = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $ftpShell = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $userPassword = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $gid = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $orgtype = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $organization = null;

	/**
	 * char(2)
	 * 
	 * @var string
	 */
	var $countryresident = null;

	/**
	 * char(2)
	 * 
	 * @var string
	 */
	var $countryorigin = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $gender = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $url = null;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $reason = null;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $mailPreferenceOption = null;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $usageAgreement = null;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $jobsAllowed = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $modifiedDate = null;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $emailConfirmed = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $regIP = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $regHost = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $nativeTribe = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $phone = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $proxyPassword = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $proxyUidNumber = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $givenName = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $middleName = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $surname = null;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $picture = null;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $vip = null;

	/**
	 * tinyint(2)
	 * 
	 * @var integer
	 */
	var $public = null;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $params = null;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xprofiles', 'uidNumber', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->givenName) == '') 
		{
			$this->setError(JText::_('MEMBER_MUST_HAVE_FIRST_NAME'));
			return false;
		}

		if (trim($this->surname) == '') 
		{
			$this->setError(JText::_('MEMBER_MUST_HAVE_LAST_NAME'));
			return false;
		}

		return true;
	}

	/**
	 * Construct a query from filters
	 * 
	 * @param      array   $filters Filters to construct query from
	 * @param      boolean $admin   Admin access?
	 * @return     string SQL
	 */
	public function buildQuery($filters=array(), $admin)
	{
		// Get plugins
		JPluginHelper::importPlugin('members');
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		$select = "";
		if (!isset($filters['count'])) 
		{
			$bits = $dispatcher->trigger('onMembersContributionsCount', array($filters['authorized']));
			$select .= ', COALESCE(cv.total_count, 0) AS rcount, COALESCE(cv.resource_count, 0) AS resource_count, COALESCE(cv.wiki_count, 0) AS wiki_count ';
		}

		// Build the query
		$sqlsearch = "";
		if (isset($filters['index']) && $filters['index'] != '') 
		{
			$sqlsearch = " ((LEFT(m.surname, 1) = '" . $this->_db->getEscaped($filters['index']) . "') OR (LEFT(SUBSTRING_INDEX(m.name, ' ', -1), 1) = '" . $this->_db->getEscaped($filters['index']) . "%')) ";
		}

		if (isset($filters['contributions']))
			$sqlsearch .= ($sqlsearch ? ' AND ' : ' ') . 'cv.resource_count + cv.wiki_count >= '. $this->_db->Quote($filters['contributions']);

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			//$show = '';
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
					$sqlsearch .= " m.email=" . $this->_db->Quote($filters['search']) . " ";
				break;

				case 'uidNumber':
					$sqlsearch .= " m.uidNumber=" . $this->_db->Quote($filters['search']) . " ";
				break;

				case 'username':
					$sqlsearch .= " m.username=" . $this->_db->Quote($filters['search']) . " ";
				break;

				case 'giveName':
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= " (LOWER(m.givenName) LIKE '%" . $this->_db->getEscaped($word) . "%') OR";
					}
					$sqlsearch = substr($sqlsearch, 0, -3);
					$sqlsearch .= ") ";
				break;

				case 'surname':
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= " (LOWER(m.surname) LIKE '%" . $this->_db->getEscaped($word) . "%') OR";
					}
					$sqlsearch = substr($sqlsearch, 0, -3);
					$sqlsearch .= ") ";
				break;

				case 'name':
				default:
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= " MATCH (m.name) AGAINST ('" . $this->_db->getEscaped($word) . "' IN BOOLEAN MODE) OR"; //" (LOWER(m.givenName) LIKE '%$word%') OR (LOWER(m.surname) LIKE '%$word%') OR (LOWER(m.middleName) LIKE '%$word%') OR (LOWER(m.name) LIKE '%$word%') OR";
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
		if (!isset($filters['count']) || !$filters['count'] || isset($filters['contributions']))
		{
			$query .= ($filters['show'] == 'contributors' ? 'INNER' : 'LEFT') . ' JOIN #__contributors_view AS cv ON m.uidNumber = cv.uidNumber';
		}
		//$query .= " LEFT JOIN #__users AS u ON u.id=m.uidNumber";
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

		return $query;
	}

	/**
	 * Get a record count based off of filters passed
	 * 
	 * @param      array   $filters Filters to construct query from
	 * @param      boolean $admin   Admin access?
	 * @return     integer
	 */
	public function getCount($filters=array(), $admin=false)
	{
		$filters['count'] = true;
		if ($admin) {
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
	 * @param      array   $filters Filters to construct query from
	 * @param      boolean $admin   Admin access?
	 * @return     array
	 */
	public function getRecords($filters=array(), $admin=false)
	{
		if ($admin) 
		{
			$filters['authorized'] = true;
			//$filters['count'] = true;
		}

		if ($filters['sortby'] == 'fullname ASC') 
		{
			$filters['sortby'] = 'lname ASC, fname ASC';
		}

		$query  = "SELECT m.uidNumber, m.username, m.name, m.givenName, m.givenName AS fname, m.middleName, m.middleName AS mname, m.surname, m.surname AS lname, m.organization, m.email, m.vip, m.public, m.picture, m.emailConfirmed, NULL AS lastvisitDate ";
		/*$query .= "CASE WHEN m.surname IS NOT NULL AND m.surname != '' AND m.surname != '&nbsp;' AND m.givenName IS NOT NULL AND m.givenName != '' AND m.givenName != '&bnsp;' THEN
		   CONCAT(m.surname, ', ', m.givenName, COALESCE(CONCAT(' ', m.middleName), ''))
		ELSE
		   COALESCE(m.name, '')
		END AS fullname ";*/
		/*$query  .= "CASE WHEN m.givenName IS NOT NULL AND m.givenName != '' AND m.givenName != '&nbsp;' THEN m.givenName ELSE SUBSTRING_INDEX(m.name, ' ', 1) END AS fname,
					CASE WHEN m.middleName IS NOT NULL AND m.middleName != '' AND m.middleName != '&nbsp;' THEN m.middleName ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(m.name,' ', 2), ' ',-1) END AS mname,
					CASE WHEN m.surname IS NOT NULL AND m.surname != '' AND m.surname != '&nbsp;' THEN m.surname ELSE SUBSTRING_INDEX(m.name, ' ', -1) END AS lname ";*/
		$query .= $this->buildQuery($filters, $admin);
		$query .= " GROUP BY m.uidNumber";
		if (isset($filters['sortby']) && $filters['sortby'] != '') 
		{
			$query .= " ORDER BY ";
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
	 * @param      string $select Fields to return
	 * @param      string $where  Filters
	 * @return     array
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
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
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
				
				$search[] = "(m.uidNumber='" . $this->_db->getEscaped($word) . "')";
				$search[] = "(LOWER(m.email) LIKE '%" . $this->_db->getEscaped($word) . "%')";
				$search[] = "(LOWER(m.username) LIKE '%" . $this->_db->getEscaped($word) . "%')";
				$search[] = "(LOWER(m.givenName) LIKE '%" . $this->_db->getEscaped($word) . "%')";
				$search[] = "(LOWER(m.surname) LIKE '%" . $this->_db->getEscaped($word) . "%')";
				$search[] = "(MATCH (m.name) AGAINST ('" . $this->_db->getEscaped($word) . "' IN BOOLEAN MODE))";
			}
			
			$where[] = "(" . implode(" OR ", $search) . ")";
		}

		if (isset($filters['public']) && $filters['public'] >= 0) 
		{
			$where[] = "m.`public`=" . $this->_db->Quote($filters['public']);
		}
		if (isset($filters['emailConfirmed']) && $filters['emailConfirmed'] != 0) 
		{
			if ($filters['emailConfirmed'] == 1)
			{
				$where[] = "m.`emailConfirmed`=" . $this->_db->Quote($filters['emailConfirmed']);
			}
			else
			{
				$where[] = "m.`emailConfirmed` < 0";
			}
		}
		if (isset($filters['registerDate']) && $filters['registerDate'] != '') 
		{
			$where[] = "m.`registerDate`>=" . $this->_db->Quote($filters['registerDate']);
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
	 * @param      array   $filters Filters to construct query from
	 * @return     integer
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
	 * @param      array   $filters Filters to construct query from
	 * @return     array
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
		//$query .= " GROUP BY m.uidNumber";
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

