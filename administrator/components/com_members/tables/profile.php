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


class MembersProfile extends JTable 
{
	var $uidNumber = null;
	var $name = null;
	var $username = null;
	var $email = null;
	var $registerDate = null;
	var $gidNumber = null;
	var $homeDirectory = null;
	var $loginShell = null;
	var $ftpShell = null;
	var $userPassword = null;
	var $gid = null;
	var $orgtype = null;
	var $organization = null;
	var $countryresident = null;
	var $countryorigin = null;
	var $gender = null;
	var $url = null;
	var $reason = null;
	var $mailPreferenceOption = null;
	var $usageAgreement = null;
	var $jobsAllowed = null;
	var $modifiedDate = null;
	var $emailConfirmed = null;
	var $regIP = null;
	var $regHost = null;
	var $nativeTribe = null;
	var $phone = null;
	var $proxyPassword = null;
	var $proxyUidNumber = null;
	var $givenName = null;
	var $middleName = null;
	var $surname = null;
	var $picture = null;
	var $vip = null;
	var $public = null;
	var $params = null;
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__xprofiles', 'uidNumber', $db );
	}
	
	//-----------
	
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
	
	//-----------
	
	public function buildQuery( $filters=array(), $admin ) 
	{
		// Get plugins
		JPluginHelper::importPlugin( 'members' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Trigger the functions that return the areas we'll be using
		$bits = $dispatcher->trigger( 'onMembersContributionsCount', array($filters['authorized']) );
		
		$select = "";
		if (!isset($filters['count'])) {
			if ($bits) {
				$s = array();
				$select .= ", (";
				foreach ($bits as $bit) 
				{
					$s[] = ($bit != '') ? "(".$bit.")" : '';
				}
				$select .= implode(" + ",$s);
				$select .= " ) AS rcount ";
			}
		}
		
		// Build the query
		$sqlsearch = "";
		$contrib_filter = '';
		if ($filters['show'] == 'contributors') {
			if ($bits) {
				$s = array();
				$contrib_filter .= " (";
				foreach ($bits as $bit) 
				{
					$s[] = ($bit != '') ? "(".$bit.")" : '';
				}
				$contrib_filter .= implode(" + ",$s);
				if (isset($filters['contributions']) && $filters['contributions'] > 0) {
					$contrib_filter .= " > ".$filters['contributions'].")";
				} else {
					$contrib_filter .= " > 0)";
				}
			}
		} 
		
		if (isset($filters['index']) && $filters['index'] != '') {
			if ($sqlsearch) {
				$sqlsearch .= " AND";
			}
			$sqlsearch .= " ( (LEFT(m.surname, 1) = '".$filters['index']."') OR (LEFT(SUBSTRING_INDEX(m.name, ' ', -1), 1) = '".$filters['index']."%') ) ";
		}
		
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

		$query  = $select."FROM $this->_tbl AS m";
		//$query .= " LEFT JOIN #__users AS u ON u.id=m.uidNumber";
		if (isset($filters['sortby']) && $filters['sortby'] == "RAND()") {
			$query .= " LEFT JOIN #__xprofiles_bio AS b ON b.uidNumber=m.uidNumber";
		}
	
		if ($contrib_filter)
			$sqlsearch = $sqlsearch ? $sqlsearch. ' AND '.$contrib_filter : $contrib_filter;	
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
	
	//-----------
	
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
	
	//-----------
	
	public function getRecords( $filters=array(), $admin=false ) 
	{
		if ($admin) {
			$filters['authorized'] = true;
		}
		
		if ($filters['sortby'] == 'fullname ASC') {
			$filters['sortby'] = 'lname ASC, fname ASC';
		}
		
		$query  = "SELECT m.uidNumber, m.username, m.name, m.givenName, m.middleName, m.surname, m.organization, m.email, m.vip, m.public, m.picture, NULL AS lastvisitDate, ";
		/*$query .= "CASE WHEN m.surname IS NOT NULL AND m.surname != '' AND m.surname != '&nbsp;' AND m.givenName IS NOT NULL AND m.givenName != '' AND m.givenName != '&bnsp;' THEN
		   CONCAT(m.surname, ', ', m.givenName, COALESCE(CONCAT(' ', m.middleName), ''))
		ELSE
		   COALESCE(m.name, '')
		END AS fullname ";*/
		$query  .= "CASE WHEN m.givenName IS NOT NULL AND m.givenName != '' AND m.givenName != '&nbsp;' THEN m.givenName ELSE SUBSTRING_INDEX(m.name, ' ', 1) END AS fname,
					CASE WHEN m.middleName IS NOT NULL AND m.middleName != '' AND m.middleName != '&nbsp;' THEN m.middleName ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(m.name,' ', 2), ' ',-1) END AS mname,
					CASE WHEN m.surname IS NOT NULL AND m.surname != '' AND m.surname != '&nbsp;' THEN m.surname ELSE SUBSTRING_INDEX(m.name, ' ', -1) END AS lname ";
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
				default:
					$query .= "lname ASC, fname ASC";
				break;
				case 'RAND()':
					$query .= "RAND()";
				break;
			}
		}
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public public function selectWhere( $select, $where ) 
	{
		$query = "SELECT $select FROM $this->_tbl WHERE $where";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

