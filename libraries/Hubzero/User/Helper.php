<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

class Hubzero_User_Helper
{
	public function random_password($length = 8) 
	{
		$genpass = '';
		$salt = "abchefghjkmnpqrstuvwxyz0123456789";
		
		srand((double)microtime()*1000000);
		
		$i = 0;
		
		while ($i < $length) 
		{
			$num = rand() % 33;
			$tmp = substr($salt, $num, 1);
			$genpass = $genpass . $tmp;
			$i++;
		}

		return($genpass);
	}
	
	//-----------

	public function encrypt_password($password) 
	{
		return("{MD5}" . base64_encode(pack('H*', md5($password))));
	}
	
	//-----------

	public function getXDomainId($domain)
	{
		$db =& JFactory::getDBO();

		if (empty($domain) || ($domain == 'hzldap'))
			return false;

		$query = 'SELECT domain_id FROM #__xdomains WHERE ' .
			 '#__xdomains.domain=' . $db->Quote($domain) . ';';

		$db->setQuery( $query );

		$result = $db->loadObject();
		
		if (empty($result))
			return false;

		return $result->domain_id;
	}
	
	//-----------

	public public function getXDomainUserId($domain_username, $domain)
	{
		$db =& JFactory::getDBO();

		if (empty($domain) || ($domain == 'hzldap'))
			return $domain_username;

		$query = 'SELECT uidNumber FROM #__xdomain_users,#__xdomains WHERE ' .
				 '#__xdomains.domain_id=#__xdomain_users.domain_id AND ' .
				 '#__xdomains.domain=' . $db->Quote($domain) . ' AND ' .
				 '#__xdomain_users.domain_username=' . $db->Quote($domain_username);

		$db->setQuery( $query );

		$result = $db->loadObject();
		
		if (empty($result))
			return false;

		return $result->uidNumber;
	}
	
	//-----------

	public public function deleteXDomainUserId($id)
	{
		$db =& JFactory::getDBO();

		if (empty($id))
			return false;

		$id = intval($id);

		if ($id <= 0)
			return false;

		$query = 'DELETE FROM #__xdomain_users WHERE uidNumber=' . $db->Quote($id) . ';';

		$db->setQuery($query);

		$db->query();

		return true;
	}
	
	//-----------

	public function isXDomainUser($uid)
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT uidNumber FROM #__xdomain_users WHERE #__xdomain_users.uidNumber=' . $db->Quote($uid);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
			return false;

		return true;
	}

	//-----------

	public function createXDomain($domain)
	{
		$db =& JFactory::getDBO();

		if (empty($domain) || $domain == 'hzldap')
			return false;

		$query = 'SELECT domain_id FROM #__xdomains WHERE ' .
				 '#__xdomains.domain=' . $db->Quote($domain);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
		{
			$query = 'INSERT INTO #__xdomains (domain) VALUES (' . $db->Quote($domain) . ')';
			
			$db->setQuery($query);

			$db->query();

			$domain_id = $db->insertid();
		}
		else
			$domain_id = $result->domain_id;

		return $domain_id;
	}
	
	//-----------

	public function setXDomainUserId($domain_username, $domain, $uidNumber)
	{
		return Hubzero_User_Helper::mapXDomainUser($domain_username, $domain, $uidNumber);
	}

	//-----------

	public function mapXDomainUser($domain_username, $domain, $uidNumber)
	{
		$db =& JFactory::getDBO();

		if (empty($domain))
			return 0;

		$query = 'SELECT domain_id FROM #__xdomains WHERE ' .
				 '#__xdomains.domain=' . $db->Quote($domain);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
		{
			$query = 'INSERT INTO #__xdomains (domain) VALUES (' . $db->Quote($domain) . ')';
			
			$db->setQuery($query);

			$db->query();

			$domain_id = $db->insertid();
		}
		else
			$domain_id = $result->domain_id;

		$query = 'INSERT INTO #__xdomain_users (domain_id,domain_username,uidNumber) ' .
			' VALUES (' . $db->Quote($domain_id) . ',' . 
			$db->Quote($domain_username) . ',' . $db->Quote($uidNumber) . ')';

		$db->setQuery($query);

		if (!$db->query())
		{
			return false;
		}

		return true;
	}
	
	//-----------

	public function getGroups($uid, $type='all', $cat = null)
	{
		$db =& JFactory::getDBO();
		
		$g = '';
		if ($cat) {
			$g .= "(g.type='".$cat."' OR g.type='3') AND";
		}

		// Get all groups the user is a member of
		$query1 = "SELECT g.gidNumber, g.published, g.cn, g.description, g.join_policy, '1' AS registered, '0' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_applicants AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query2 = "SELECT g.gidNumber, g.published, g.cn, g.description, g.join_policy, '1' AS registered, '1' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_members AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query3 = "SELECT g.gidNumber, g.published, g.cn, g.description, g.join_policy, '1' AS registered, '1' AS regconfirmed, '1' AS manager FROM #__xgroups AS g, #__xgroups_managers AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query4 = "SELECT g.gidNumber, g.published, g.cn, g.description, g.join_policy, '0' AS registered, '1' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_invitees AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		
		switch ($type) 
		{
			case 'all':
				$query = "( $query1 ) UNION ( $query2 ) UNION ( $query3 ) UNION ( $query4 )";
			break;
			case 'applicants':
				$query = $query1." ORDER BY description, cn";
			break;
			case 'members':
				$query = $query2." ORDER BY description, cn";
			break;
			case 'managers':
				$query = $query3." ORDER BY description, cn";
			break;
			case 'invitees':
				$query = $query4." ORDER BY description, cn";
			break;
		}
		
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (empty($result))
			return false;

		return $result;
	}
}
