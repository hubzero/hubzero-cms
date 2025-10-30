<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

/**
 * Helper class for users
 */
class Helper
{
	/**
	 * Generate a random password
	 *
	 * @param   integer  $length
	 * @return  string
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

		return $genpass;
	}

	/**
	 * Encrypt a password
	 *
	 * @param   string  $password
	 * @return  string
	 */
	public static function encrypt_password($password)
	{
		return "{MD5}" . base64_encode(pack('H*', md5($password)));
	}

	/**
	 * Get domain ID
	 *
	 * @param   string  $domain
	 * @return  mixed
	 */
	public static function getXDomainId($domain)
	{
		$db = \App::get('db');

		if (empty($domain) || ($domain == 'hubzero'))
		{
			return false;
		}

		$query = 'SELECT domain_id FROM `#__xdomains` WHERE domain=' . $db->quote($domain) . ';';
		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
		{
			return false;
		}

		return $result->domain_id;
	}

	/**
	 * Get domain user ID
	 *
	 * @param   string  $domain_username
	 * @param   string  $domain
	 * @return  mixed
	 */
	public static function getXDomainUserId($domain_username, $domain)
	{
		$db = \App::get('db');

		if (empty($domain) || ($domain == 'hubzero'))
		{
			return $domain_username;
		}

		$query = 'SELECT uidNumber FROM #__xdomain_users,#__xdomains WHERE ' .
				 '#__xdomains.domain_id=#__xdomain_users.domain_id AND ' .
				 '#__xdomains.domain=' . $db->quote($domain) . ' AND ' .
				 '#__xdomain_users.domain_username=' . $db->quote($domain_username);
		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
		{
			return false;
		}

		return $result->uidNumber;
	}

	/**
	 * Delete a record by user ID
	 *
	 * @param   integer  $id
	 * @return  boolean
	 */
	public static function deleteXDomainUserId($id)
	{
		$db = \App::get('db');

		if (empty($id))
		{
			return false;
		}

		$id = intval($id);

		if ($id <= 0)
		{
			return false;
		}

		$query = 'DELETE FROM `#__xdomain_users` WHERE uidNumber=' . $db->quote($id) . ';';

		$db->setQuery($query);

		$db->query();

		return true;
	}

	/**
	 * Check if a user has a domain record
	 *
	 * @param   integer  $uid
	 * @return  boolean
	 */
	public static function isXDomainUser($uid)
	{
		$db = \App::get('db');

		$query = 'SELECT uidNumber FROM `#__xdomain_users` WHERE #__xdomain_users.uidNumber=' . $db->quote($uid);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
		{
			return false;
		}

		return true;
	}

	/**
	 * Creeate a domain record
	 *
	 * @param   string   $domain
	 * @return  boolean
	 */
	public static function createXDomain($domain)
	{
		$db = \App::get('db');

		if (empty($domain) || ($domain == 'hubzero'))
		{
			return false;
		}

		$query = 'SELECT domain_id FROM `#__xdomains` WHERE ' .
				 '#__xdomains.domain=' . $db->quote($domain);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
		{
			$query = 'INSERT INTO `#__xdomains` (domain) VALUES (' . $db->quote($domain) . ')';

			$db->setQuery($query);

			$db->query();

			$domain_id = $db->insertid();
		}
		else
		{
			$domain_id = $result->domain_id;
		}

		return $domain_id;
	}

	/**
	 * Set a domain for a user
	 *
	 * @param   string   $domain_username
	 * @param   string   $domain
	 * @param   integer  $uidNumber
	 * @return  bool
	 */
	public static function setXDomainUserId($domain_username, $domain, $uidNumber)
	{
		return self::mapXDomainUser($domain_username, $domain, $uidNumber);
	}

	/**
	 * Map a domain to a user
	 *
	 * @param   string   $domain_username
	 * @param   string   $domain
	 * @param   integer  $uidNumber
	 * @return  boolean
	 */
	public static function mapXDomainUser($domain_username, $domain, $uidNumber)
	{
		$db = \App::get('db');

		if (empty($domain))
		{
			return 0;
		}

		$query = 'SELECT domain_id FROM `#__xdomains` WHERE ' .
				 '#__xdomains.domain=' . $db->quote($domain);

		$db->setQuery($query);

		$result = $db->loadObject();

		if (empty($result))
		{
			$query = 'INSERT INTO `#__xdomains` (domain) VALUES (' . $db->quote($domain) . ')';

			$db->setQuery($query);

			$db->query();

			$domain_id = $db->insertid();
		}
		else
		{
			$domain_id = $result->domain_id;
		}

		$query = 'INSERT INTO `#__xdomain_users` (domain_id,domain_username,uidNumber) ' .
			' VALUES (' . $db->quote($domain_id) . ',' .
			$db->quote($domain_username) . ',' . $db->quote($uidNumber) . ')';

		$db->setQuery($query);

		if (!$db->query())
		{
			return false;
		}

		return true;
	}

	/**
	 * Get a list of groups for a user
	 *
	 * @param   string  $uid
	 * @param   string  $type
	 * @param   string  $cat
	 * @return  boolean
	 */
	public static function getGroups($uid, $type='all', $cat = null)
	{
		//return empty groups if user is not authenticated
		if ($uid == 0)
		{
			return array();
		}
		
		$db = \App::get('db');

		$g = '';
		if ($cat == 1)
		{
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
		{
			return array();
		}

		return $result;
	}

	/**
	 * Remove User From Groups
	 *
	 * @param   integer  $uid
	 * @return  boolean
	 */
	public static function removeUserFromGroups($uid)
	{
		$db = \App::get('db');
		$tables = array('#__xgroups_members', '#__xgroups_managers', '#__xgroups_invitees', '#__xgroups_applicants');

		foreach ($tables as $table)
		{
			$sql = "DELETE FROM `".$table."` WHERE uidNumber=" . $db->quote($uid);
			$db->setQuery($sql);
			$db->query();
		}

		return true;
	}

	/**
	 * Get courses for a user
	 *
	 * @param   string  $uid
	 * @param   string  $type
	 * @param   string  $cat
	 * @return  boolean
	 */
	public static function getCourses($uid, $type='all', $cat = null)
	{
		$db = \App::get('db');

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
		{
			return false;
		}

		return $result;
	}

	/**
	 * Get common groups between two users
	 *
	 * @param   integer  $uid
	 * @param   integer  $pid
	 * @return  boolean
	 */
	public static function getCommonGroups($uid, $pid)
	{
		// Get the groups the visiting user
		$xgroups = self::getGroups($uid, 'all', 1);

		$usersgroups = array();
		if (!empty($xgroups))
		{
			foreach ($xgroups as $group)
			{
				if ($group->regconfirmed)
				{
					$usersgroups[] = $group->cn;
				}
			}
		}

		// Get the groups of the profile
		$pgroups = self::getGroups($pid, 'all', 1);

		// Get the groups the user has access to
		$profilesgroups = array();
		if (!empty($pgroups))
		{
			foreach ($pgroups as $group)
			{
				if ($group->regconfirmed)
				{
					$profilesgroups[] = $group->cn;
				}
			}
		}

		// Find the common groups
		return array_intersect($usersgroups, $profilesgroups);
	}
}
