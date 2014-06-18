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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\User;

/**
 * Helper class for users
 */
class Helper
{

	/**
	 * Short description for 'random_password'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $length Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function random_password($length = 8)
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

	/**
	 * Short description for 'encrypt_password'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $password Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function encrypt_password($password)
	{
		return("{MD5}" . base64_encode(pack('H*', md5($password))));
	}

	/**
	 * Short description for 'getXDomainId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $domain Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function getXDomainId($domain)
	{
		$db = \JFactory::getDBO();

		if (empty($domain) || ($domain == 'hubzero'))
			return false;

		$query = 'SELECT domain_id FROM `#__xdomains` WHERE domain=' . $db->Quote($domain) . ';';

		$db->setQuery( $query );

		$result = $db->loadObject();

		if (empty($result))
			return false;

		return $result->domain_id;
	}

	/**
	 * Short description for 'getXDomainUserId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $domain_username Parameter description (if any) ...
	 * @param      string $domain Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function getXDomainUserId($domain_username, $domain)
	{
		$db = \JFactory::getDBO();

		if (empty($domain) || ($domain == 'hubzero'))
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

	/**
	 * Short description for 'deleteXDomainUserId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function deleteXDomainUserId($id)
	{
		$db = \JFactory::getDBO();

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

	/**
	 * Short description for 'isXDomainUser'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function isXDomainUser($uid)
	{
		$db = \JFactory::getDBO();

		$query = 'SELECT uidNumber FROM #__xdomain_users WHERE #__xdomain_users.uidNumber=' . $db->Quote($uid);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
			return false;

		return true;
	}

	/**
	 * Short description for 'createXDomain'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $domain Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function createXDomain($domain)
	{
		$db = \JFactory::getDBO();

		if (empty($domain) || ($domain == 'hubzero'))
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

	/**
	 * Short description for 'setXDomainUserId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $domain_username Parameter description (if any) ...
	 * @param      unknown $domain Parameter description (if any) ...
	 * @param      unknown $uidNumber Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function setXDomainUserId($domain_username, $domain, $uidNumber)
	{
		return self::mapXDomainUser($domain_username, $domain, $uidNumber);
	}

	/**
	 * Short description for 'mapXDomainUser'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $domain_username Parameter description (if any) ...
	 * @param      unknown $domain Parameter description (if any) ...
	 * @param      unknown $uidNumber Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function mapXDomainUser($domain_username, $domain, $uidNumber)
	{
		$db = \JFactory::getDBO();

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

	/**
	 * Short description for 'getGroups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      string $cat Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function getGroups($uid, $type='all', $cat = null)
	{
		$db = \JFactory::getDBO();

		$g = '';
		if ($cat == 1) {
			$g .= "(g.type='".$cat."' OR g.type='3') AND";
		}
		elseif ($cat !== null)
		{
			$g .= "g.type=" . $db->quote($cat) . " AND ";
		}

		// Get all groups the user is a member of
		$query1 = "SELECT g.gidNumber, g.published, g.approved, g.cn, g.description, g.join_policy, '1' AS registered, '0' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_applicants AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query2 = "SELECT g.gidNumber, g.published, g.approved, g.cn, g.description, g.join_policy, '1' AS registered, '1' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_members AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query3 = "SELECT g.gidNumber, g.published, g.approved, g.cn, g.description, g.join_policy, '1' AS registered, '1' AS regconfirmed, '1' AS manager FROM #__xgroups AS g, #__xgroups_managers AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query4 = "SELECT g.gidNumber, g.published, g.approved, g.cn, g.description, g.join_policy, '0' AS registered, '1' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_invitees AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;

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
	
	/**
	 * Remove User From Groups
	 * 
	 * @param      string $uid 
	 * @return     boolean
	 */
	public static function removeUserFromGroups( $uid )
	{
		$db = \JFactory::getDBO();
		$tables = array('#__xgroups_members', '#__xgroups_managers', '#__xgroups_invitees', '#__xgroups_applicants');
		
		foreach ($tables as $table)
		{
			$sql = "DELETE FROM `".$table."` WHERE uidNumber=" . $db->quote( $uid );
			$db->setQuery( $sql );
			$db->query();
		}
		
		return true;
	}

	/**
	 * Short description for 'getCourses'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      string $cat Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function getCourses($uid, $type='all', $cat = null)
	{
		$db = \JFactory::getDBO();

		$g = '';
		if ($cat == 1) {
			$g .= "(g.type='".$cat."' OR g.type='3') AND";
		}

		// Get all courses the user is a member of
		$query1 = "SELECT g.id, g.state, g.alias, g.title, g.join_policy, '1' AS registered, '0' AS regconfirmed, '0' AS manager FROM #__courses AS g, #__courses_applicants AS m WHERE $g m.course_id=g.id AND m.user_id=".$uid;
		$query2 = "SELECT g.id, g.state, g.alias, g.title, g.join_policy, '1' AS registered, '1' AS regconfirmed, '0' AS manager FROM #__courses AS g, #__courses_members AS m WHERE $g m.course_id=g.id AND m.user_id=".$uid;
		$query3 = "SELECT g.id, g.state, g.alias, g.title, g.join_policy, '1' AS registered, '1' AS regconfirmed, '1' AS manager FROM #__courses AS g, #__courses_managers AS m WHERE $g m.course_id=g.id AND m.user_id=".$uid;
		$query4 = "SELECT g.id, g.state, g.alias, g.title, g.join_policy, '0' AS registered, '1' AS regconfirmed, '0' AS manager FROM #__courses AS g, #__courses_invitees AS m WHERE $g m.course_id=g.id AND m.user_id=".$uid;

		switch ($type)
		{
			case 'all':
				$query = "( $query1 ) UNION ( $query2 ) UNION ( $query3 ) UNION ( $query4 )";
			break;
			case 'applicants':
				$query = $query1." ORDER BY title, alias";
			break;
			case 'members':
				$query = $query2." ORDER BY title, alias";
			break;
			case 'managers':
				$query = $query3." ORDER BY title, alias";
			break;
			case 'invitees':
				$query = $query4." ORDER BY title, alias";
			break;
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (empty($result))
			return false;

		return $result;
	}

	/**
	 * Short description for 'getCommonGroups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      string $pid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public static function getCommonGroups( $uid, $pid )
	{
		$uprofile = Profile::getInstance($uid);
		
		// Get the groups the visiting user
		$xgroups = (is_object($uprofile)) ? $uprofile->getGroups('all') : array();
		$usersgroups = array();
		if (!empty($xgroups)) {
			foreach ($xgroups as $group)
			{
				if ($group->regconfirmed) {
					$usersgroups[] = $group->cn;
				}
			}
		}

		// Get the groups of the profile
		$pprofile = Profile::getInstance($pid);
		$pgroups = (is_object($pprofile)) ? $pprofile->getGroups('all') : array();
		// Get the groups the user has access to
		$profilesgroups = array();
		if (!empty($pgroups)) {
			foreach ($pgroups as $group)
			{
				if ($group->regconfirmed) {
					$profilesgroups[] = $group->cn;
				}
			}
		}
		
		// Find the common groups
		$common = array_intersect($usersgroups, $profilesgroups);
		
		//return common groups
		return $common;
	}
}

