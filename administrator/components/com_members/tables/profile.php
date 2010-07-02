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
		if ($filters['show'] == 'contributors') {
			if ($bits) {
				$s = array();
				$sqlsearch .= " (";
				foreach ($bits as $bit) 
				{
					$s[] = ($bit != '') ? "(".$bit.")" : '';
				}
				$sqlsearch .= implode(" + ",$s);
				if (isset($filters['contributions']) && $filters['contributions'] > 0) {
					$sqlsearch .= " > ".$filters['contributions'].")";
				} else {
					$sqlsearch .= " > 0)";
				}
			}
		} 
		
		if (isset($filters['index']) && $filters['index'] != '') {
			if ($filters['show'] == 'contributors') {
				$sqlsearch .= " AND";
			}
			$sqlsearch .= " ( (LOWER(m.surname) LIKE '".$filters['index']."%') ) ";
		}
		
		if (isset($filters['search']) && $filters['search'] != '') {
			//$show = '';
			$words = explode(' ', $filters['search']);
			if ($filters['show'] == 'contributors' || (isset($filters['index']) && $filters['index'] != '')) {
				$sqlsearch .= " AND";
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
						$sqlsearch .= " (LOWER(m.givenName) LIKE '%$word%') OR (LOWER(m.surname) LIKE '%$word%') OR (LOWER(m.middleName) LIKE '%$word%') OR";
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
		
		$query  = "SELECT m.uidNumber, m.username, m.name, m.givenName, m.middleName, m.surname, m.organization, m.email, m.vip, m.public, m.picture, NULL AS lastvisitDate, ";
		$query .= "CASE WHEN m.surname IS NOT NULL AND m.surname != '' AND m.surname != '&nbsp;' AND m.givenName IS NOT NULL AND m.givenName != '' AND m.givenName != '&bnsp;' THEN
		   CONCAT(m.surname, ', ', m.givenName, COALESCE(CONCAT(' ', m.middleName), ''))
		ELSE
		   COALESCE(m.name, '')
		END AS fullname ";
		$query .= $this->buildQuery( $filters, $admin );
		$query .= " GROUP BY m.uidNumber ORDER BY ".$filters['sortby'];
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
