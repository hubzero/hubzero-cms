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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'MembersProfile'
 * 
 * Long description (if any) ...
 */
class MembersProfile extends JTable
{

	/**
	 * Description for 'uidNumber'
	 * 
	 * @var unknown
	 */
	var $uidNumber = null;

	/**
	 * Description for 'name'
	 * 
	 * @var unknown
	 */
	var $name = null;

	/**
	 * Description for 'username'
	 * 
	 * @var unknown
	 */
	var $username = null;

	/**
	 * Description for 'email'
	 * 
	 * @var unknown
	 */
	var $email = null;

	/**
	 * Description for 'registerDate'
	 * 
	 * @var unknown
	 */
	var $registerDate = null;

	/**
	 * Description for 'gidNumber'
	 * 
	 * @var unknown
	 */
	var $gidNumber = null;

	/**
	 * Description for 'homeDirectory'
	 * 
	 * @var unknown
	 */
	var $homeDirectory = null;

	/**
	 * Description for 'loginShell'
	 * 
	 * @var unknown
	 */
	var $loginShell = null;

	/**
	 * Description for 'ftpShell'
	 * 
	 * @var unknown
	 */
	var $ftpShell = null;

	/**
	 * Description for 'userPassword'
	 * 
	 * @var unknown
	 */
	var $userPassword = null;

	/**
	 * Description for 'gid'
	 * 
	 * @var unknown
	 */
	var $gid = null;

	/**
	 * Description for 'orgtype'
	 * 
	 * @var unknown
	 */
	var $orgtype = null;

	/**
	 * Description for 'organization'
	 * 
	 * @var unknown
	 */
	var $organization = null;

	/**
	 * Description for 'countryresident'
	 * 
	 * @var unknown
	 */
	var $countryresident = null;

	/**
	 * Description for 'countryorigin'
	 * 
	 * @var unknown
	 */
	var $countryorigin = null;

	/**
	 * Description for 'gender'
	 * 
	 * @var unknown
	 */
	var $gender = null;

	/**
	 * Description for 'url'
	 * 
	 * @var unknown
	 */
	var $url = null;

	/**
	 * Description for 'reason'
	 * 
	 * @var unknown
	 */
	var $reason = null;

	/**
	 * Description for 'mailPreferenceOption'
	 * 
	 * @var unknown
	 */
	var $mailPreferenceOption = null;

	/**
	 * Description for 'usageAgreement'
	 * 
	 * @var unknown
	 */
	var $usageAgreement = null;

	/**
	 * Description for 'jobsAllowed'
	 * 
	 * @var unknown
	 */
	var $jobsAllowed = null;

	/**
	 * Description for 'modifiedDate'
	 * 
	 * @var unknown
	 */
	var $modifiedDate = null;

	/**
	 * Description for 'emailConfirmed'
	 * 
	 * @var unknown
	 */
	var $emailConfirmed = null;

	/**
	 * Description for 'regIP'
	 * 
	 * @var unknown
	 */
	var $regIP = null;

	/**
	 * Description for 'regHost'
	 * 
	 * @var unknown
	 */
	var $regHost = null;

	/**
	 * Description for 'nativeTribe'
	 * 
	 * @var unknown
	 */
	var $nativeTribe = null;

	/**
	 * Description for 'phone'
	 * 
	 * @var unknown
	 */
	var $phone = null;

	/**
	 * Description for 'proxyPassword'
	 * 
	 * @var unknown
	 */
	var $proxyPassword = null;

	/**
	 * Description for 'proxyUidNumber'
	 * 
	 * @var unknown
	 */
	var $proxyUidNumber = null;

	/**
	 * Description for 'givenName'
	 * 
	 * @var unknown
	 */
	var $givenName = null;

	/**
	 * Description for 'middleName'
	 * 
	 * @var unknown
	 */
	var $middleName = null;

	/**
	 * Description for 'surname'
	 * 
	 * @var unknown
	 */
	var $surname = null;

	/**
	 * Description for 'picture'
	 * 
	 * @var unknown
	 */
	var $picture = null;

	/**
	 * Description for 'vip'
	 * 
	 * @var unknown
	 */
	var $vip = null;

	/**
	 * Description for 'public'
	 * 
	 * @var unknown
	 */
	var $public = null;

	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $params = null;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__xprofiles', 'uidNumber', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim( $this->givenName ) == '') {
			$this->setError( JText::_('MEMBER_MUST_HAVE_FIRST_NAME') );
			return false;
		}

		if (trim( $this->surname ) == '') {
			$this->setError( JText::_('MEMBER_MUST_HAVE_LAST_NAME') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      unknown $admin Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery( $filters=array(), $admin )
	{
		// Get plugins
		JPluginHelper::importPlugin( 'members' );
		$dispatcher =& JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using

		$select = "";
		if (!isset($filters['count'])) {
			$bits = $dispatcher->trigger( 'onMembersContributionsCount', array($filters['authorized']) );
			$select .= ', COALESCE(cv.total_count, 0) AS rcount, COALESCE(cv.resource_count, 0) AS resource_count, COALESCE(cv.wiki_count, 0) AS wiki_count ';
		}

		// Build the query
		$sqlsearch = "";
		if (isset($filters['index']) && $filters['index'] != '') {
			$sqlsearch = " ( (LEFT(m.surname, 1) = '".$filters['index']."') OR (LEFT(SUBSTRING_INDEX(m.name, ' ', -1), 1) = '".$filters['index']."%') ) ";
		}

		if (isset($filters['contributions']))
			$sqlsearch .= ($sqlsearch ? ' AND ' : ' ') . 'cv.resource_count + cv.wiki_count >= '. $filters['contributions'];

		if (isset($filters['search']) && $filters['search'] != '') {
			//$show = '';
			$words = explode(' ', $filters['search']);
			if ($sqlsearch) {
				$sqlsearch .= " AND";
			}
			if (!isset($filters['search_field'])) {
				$filters['search_field'] = 'name';
			}
			switch ($filters['search_field'])
			{
				case 'email':
					$sqlsearch .= " m.email='".$filters['search']."' ";
				break;

				case 'uidNumber':
					$sqlsearch .= " m.uidNumber='".$filters['search']."' ";
				break;

				case 'username':
					$sqlsearch .= " m.username='".$filters['search']."' ";
				break;

				case 'giveName':
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= " (LOWER(m.givenName) LIKE '%$word%') OR";
					}
					$sqlsearch = substr($sqlsearch, 0, -3);
					$sqlsearch .= ") ";
				break;

				case 'surname':
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= " (LOWER(m.surname) LIKE '%$word%') OR";
					}
					$sqlsearch = substr($sqlsearch, 0, -3);
					$sqlsearch .= ") ";
				break;

				case 'name':
				default:
					$sqlsearch .= " (";
					foreach ($words as $word)
					{
						$sqlsearch .= ' MATCH (m.name) AGAINST (\''.$word.'\' IN BOOLEAN MODE) OR'; //" (LOWER(m.givenName) LIKE '%$word%') OR (LOWER(m.surname) LIKE '%$word%') OR (LOWER(m.middleName) LIKE '%$word%') OR (LOWER(m.name) LIKE '%$word%') OR";
					}
					$sqlsearch = substr($sqlsearch, 0, -3);
					$sqlsearch .= ") ";
				break;
			}
		}

		if (isset($filters['sortby']) && $filters['sortby'] == "RAND()") {
			$select .= ", b.bio ";
		}

		$query  = $select."FROM $this->_tbl AS m ".($filters['show'] == 'contributors' ? 'INNER' : 'LEFT').' JOIN #__contributors_view AS cv ON m.uidNumber = cv.uidNumber';
		//$query .= " LEFT JOIN #__users AS u ON u.id=m.uidNumber";
		if (isset($filters['sortby']) && $filters['sortby'] == "RAND()") {
			$query .= " LEFT JOIN #__xprofiles_bio AS b ON b.uidNumber=m.uidNumber";
		}
		if ($sqlsearch) {
			$query .= ' WHERE'.$sqlsearch;
			if (!$admin || $filters['show'] == 'contributors' || (isset($filters['sortby']) && $filters['sortby'] == "RAND()")) {
				$query .= " AND m.public=1";
			}
			if (isset($filters['sortby']) && $filters['sortby'] == "RAND()") {
				$query .= " AND b.bio != '' AND b.bio IS NOT NULL AND m.picture != '' AND m.picture IS NOT NULL";
			}
			if ($filters['show'] == 'vips') {
				$query .= " AND m.vip=1";
			}
		} else {
			if (!$admin || $filters['show'] == 'contributors' || (isset($filters['sortby']) && $filters['sortby'] == "RAND()")) {
				$query .= " WHERE m.public=1";
				if ($filters['show'] == 'vips') {
					$query .= " AND m.vip=1";
				}
			} else {
				if ($filters['show'] == 'vips') {
					$query .= " WHERE m.vip=1";
				}
			}
		}

		return $query;
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount( $filters=array(), $admin=false )
	{
		$filters['count'] = true;
		if ($admin) {
			$filters['authorized'] = true;
		}
		$query  = "SELECT count(DISTINCT m.uidNumber) ";
		$query .= $this->buildQuery( $filters, $admin );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords( $filters=array(), $admin=false )
	{
		if ($admin) {
			$filters['authorized'] = true;
			//$filters['count'] = true;
		}

		if ($filters['sortby'] == 'fullname ASC') {
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
		$query .= $this->buildQuery( $filters, $admin );
		$query .= " GROUP BY m.uidNumber";
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
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
		if (isset($filters['limit']) && $filters['limit'] && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'selectWhere'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $select Parameter description (if any) ...
	 * @param      unknown $where Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function selectWhere( $select, $where )
	{
		$query = "SELECT $select FROM $this->_tbl WHERE $where";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      unknown $admin Parameter description (if any) ...
	 * @return     string Return description (if any) ...
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
				
				$search[] = "(m.uidNumber='$word')";
				$search[] = "(LOWER(m.email) LIKE '%$word%')";
				$search[] = "(LOWER(m.username) LIKE '%$word%')";
				$search[] = "(LOWER(m.givenName) LIKE '%$word%')";
				$search[] = "(LOWER(m.surname) LIKE '%$word%')";
				$search[] = "(MATCH (m.name) AGAINST ('".$word."' IN BOOLEAN MODE))";
			}
			
			$where[] = "(" . implode(" OR ", $search) . ")";
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
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecordCount($filters=array())
	{
		$query  = "SELECT count(DISTINCT m.uidNumber) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
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
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

