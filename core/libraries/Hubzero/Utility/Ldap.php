<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

class Ldap
{
	/**
	 * Error messages
	 *
	 * @var  array
	 */
	private static $errors  = array(
		'errors'   => true,
		'fatal'	=> array(),
		'warnings' => array()
	);

	/**
	 * Success messages
	 *
	 * @var  array
	 */
	private static $success = array(
		'success'  => true,
		'added'	=> 0,
		'deleted'  => 0,
		'modified' => 0,
		'unchanged' => 0
	);

	/**
	 * Get the LDAP connection
	 *
	 * @param   integer  $debug
	 * @return  mixed
	 */
	public static function getLDO($debug = 0)
	{
		static $conn = false;

		if ($conn !== false)
		{
			return $conn;
		}

		$ldap_params = \Component::params('com_system');

		$acctman   = $ldap_params->get('ldap_managerdn','cn=admin');
		$acctmanPW = $ldap_params->get('ldap_managerpw','');
		$pldap     = $ldap_params->get('ldap_primary', 'ldap://localhost');

		$negotiate_tls = $ldap_params->get('ldap_tls', 0);
		$port = '389';

		if (!is_numeric($port))
		{
			$port = '389';

			$pattern = "/^\s*(ldap[s]{0,1}:\/\/|)([^:]*)(\:(\d+)|)\s*$/";

			if (preg_match($pattern, $pldap, $matches))
			{
				$pldap = $matches[2];

				if ($matches[1] == 'ldaps://')
				{
					$negotiate_tls = false;
				}

				if (isset($matches[4]) && is_numeric($matches[4]))
				{
					$port = $matches[4];
				}
			}
		}

		$conn = ldap_connect($pldap, $port);

		if ($conn === false)
		{
			if ($debug)
			{
				\Log::debug("getLDO(): ldap_connect($pldap,$port) failed. [" . posix_getpid() . "] " . ldap_error($conn));
			}

			return false;
		}

		if ($debug)
		{
			\Log::debug("getLDO(): ldap_connect($pldap,$port) success. ");
		}

		if (ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3) == false)
		{
			if ($debug)
			{
				\Log::debug("getLDO(): ldap_set_option(LDAP_OPT_PROTOCOL_VERSION, 3) failed: " . ldap_error($conn));
			}

			$conn = false;
			return false;
		}

		if ($debug)
		{
			\Log::debug("getLDO(): ldap_set_option(LDAP_OPT_PROTOCOL_VERSION, 3) success.");
		}

		if (ldap_set_option($conn, LDAP_OPT_RESTART, 1) == false)
		{
			if ($debug)
			{
				\Log::debug("getLDO(): ldap_set_option(LDAP_OPT_RESTART, 1) failed: " . ldap_error($conn));
			}

			$conn = false;
			return false;
		}

		if ($debug)
		{
			\Log::debug("getLDO(): ldap_set_option(LDAP_OPT_RESTART, 1) success.");
		}

		if (!ldap_set_option($conn, LDAP_OPT_REFERRALS, false))
		{
			if ($debug)
			{
				\Log::debug("getLDO(): ldap_set_option(LDAP_OPT_REFERRALS, 0) failed: " . ldap_error($conn));
			}

			$conn = false;
			return false;
		}

		if ($debug)
		{
			\Log::debug("getLDO(): ldap_set_option(LDAP_OPT_REFERRALS, 0) success.");
		}

		if ($negotiate_tls)
		{
			if (!ldap_start_tls($conn))
			{
				if ($debug)
				{
					\Log::debug("getLDO(): ldap_start_tls() failed: " . ldap_error($conn));
				}

				$conn = false;
				return false;
			}

			if ($debug)
			{
				\Log::debug("getLDO(): ldap_start_tls() success.");
			}
		}

		if (ldap_bind($conn, $acctman, $acctmanPW) == false)
		{
			$err	 = ldap_errno($conn);
			$errstr  = ldap_error($conn);
			$errstr2 = ldap_err2str($err);

			if ($debug)
			{
				\Log::debug("getLDO(): ldap_bind($acctman) failed. [" . posix_getpid() . "] " .  $errstr);
			}

			$conn = false;
			return false;
		}

		if ($debug)
		{
			\Log::debug("getLDO(): ldap_bind() success.");
		}

		return $conn;
	}

	/**
	 * Sync a user's info to LDAP
	 *
	 * @param   mixed   $user
	 * @return  boolean
	 */
	public static function syncUser($user)
	{
		$db = \App::get('db');

		if (empty($db))
		{
			self::$errors['fatal'][] = 'Error connecting to the database';
			return false;
		}

		$conn = self::getLDO();

		if (empty($conn))
		{
			self::$errors['fatal'][] = 'LDAP connection failed';
			return false;
		}

		$query = "SELECT p.uidNumber AS uidNumber, p.username AS uid, p.name AS cn, " .
				" p.gidNumber, p.homeDirectory, p.loginShell, " .
				" pwd.passhash AS userPassword, pwd.shadowLastChange, pwd.shadowMin, pwd.shadowMax, pwd.shadowWarning, " .
				" pwd.shadowInactive, pwd.shadowExpire, pwd.shadowFlag " .
				" FROM #__users AS u " .
				" LEFT JOIN #__users_password AS pwd ON u.id = pwd.user_id " .
				" LEFT JOIN #__xprofiles AS p ON u.id = p.uidNumber ";

		if (is_numeric($user) && $user >= 0)
		{
			$query .= " WHERE u.id = " . $db->quote($user) . " LIMIT 1;";
		}
		else
		{
			$query .= " WHERE u.username = " . $db->quote($user) . " LIMIT 1;";
		}

		$db->setQuery($query);
		$dbinfo = $db->loadAssoc();

		// Don't sync usernames that are negative numbers (these are auth_link temp accounts)
		if (is_numeric($dbinfo['uid']) && $dbinfo['uid'] <= 0)
		{
			return false;
		}

		if (!empty($dbinfo))
		{
			$query = "SELECT host FROM #__xprofiles_host WHERE uidNumber = " . $db->quote($dbinfo['uidNumber']) . ";";
			$db->setQuery($query);
			$dbinfo['host'] = $db->loadColumn();
		}

		$ldap_params = \Component::params('com_system');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');

		if (is_numeric($user) && $user >= 0)
		{
			$dn = 'ou=users,' . $hubLDAPBaseDN;
			$filter = '(|(uidNumber=' . $user . ')(uid=' . $dbinfo['uid'] . '))';
		}
		else
		{
			$dn = "uid=$user,ou=users," . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}

		$reqattr = array(
			'uidNumber','uid','cn','gidNumber','homeDirectory','loginShell','userPassword','shadowLastChange',
			'shadowMin','shadowMax','shadowWarning','shadowInactive','shadowExpire','shadowFlag', 'host'
		);

		$entry = ldap_search($conn, $dn, $filter, $reqattr, 0, 0, 0);
		$count = ($entry) ? ldap_count_entries($conn, $entry) : 0;

		// If there was a database entry, but there was no ldap entry, create the ldap entry
		if (!empty($dbinfo) && ($count <= 0))
		{
			$dn = "uid=" . $dbinfo['uid'] . ",ou=users," . $hubLDAPBaseDN;

			$entry = array();
			$entry['objectclass'][] = 'top';
			$entry['objectclass'][] = 'account';  // MUST uid
			$entry['objectclass'][] = 'posixAccount'; // MUST cn,gidNumber,homeDirectory,uidNumber
			$entry['objectclass'][] = 'shadowAccount';

			foreach ($dbinfo as $key=>$value)
			{
				if (is_array($value) && $value != array())
				{
					$entry[$key] = $value;
				}
				else if (!is_array($value) && $value != '')
				{
					$entry[$key] = $value;
				}
			}

			if (empty($entry['uid']) || empty($entry['cn']) || empty($entry['gidNumber']))
			{
				self::$errors['warning'][] = "User {$dbinfo['uid']} missing one of uid, cn, or gidNumber";
				return false;
			}

			if (empty($entry['homeDirectory']) || empty($entry['uidNumber']))
			{
				self::$errors['warning'][] = "User {$dbinfo['uid']} missing one of homeDirectory or uidNumber";
				return false;
			}

			$result = ldap_add($conn, $dn, $entry);

			if ($result !== true)
			{
				self::$errors['warning'][] = ldap_error($conn);
				return false;
			}
			else
			{
				++self::$success['added'];
				return true;
			}
		}

		$ldapinfo = null;

		if ($count > 0)
		{
			$firstentry = ldap_first_entry($conn, $entry);

			$attr = ldap_get_attributes($conn, $firstentry);

			if (!empty($attr))
			{
				foreach ($reqattr as $key)
				{
					unset($attr[$key]['count']);

					if (isset($attr[$key][0]))
					{
						if (count($attr[$key]) <= 1)
						{
							$ldapinfo[$key] = $attr[$key][0];
						}
						else
						{
							$ldapinfo[$key] = $attr[$key];
						}
					}
					else
					{
						$ldapinfo[$key] = null;
					}
				}
			}
		}

		// If there was no database entry, and there was no ldap entry, nothing to do
		if (empty($dbinfo) && empty($ldapinfo))
		{
			return true;
		}

		// If there was no database entry, but there was an ldap entry, delete the ldap entry
		if (!empty($ldapinfo) && empty($dbinfo))
		{
			$dn = "uid=" . $ldapinfo['uid'] . ",ou=users," . $hubLDAPBaseDN;

			$result = ldap_delete($conn, $dn);

			if ($result !== true)
			{
				self::$errors['warning'][] = ldap_error($conn);
				return false;
			}
			else
			{
				++self::$success['deleted'];
				return true;
			}
		}

		// Otherwise update the ldap entry

		if (!empty($ldapinfo['host']) && !is_array($ldapinfo['host']))
		{
			$ldapinfo['host'] = array($ldapinfo['host']);
		}

		$entry = array();

		foreach ($dbinfo as $key=>$value)
		{
			if ($ldapinfo[$key] != $dbinfo[$key])
			{
				if ($dbinfo[$key] === null)
				{
					$entry[$key] = array();
				}
				else
				{
					$entry[$key] = is_array($dbinfo[$key]) ? $dbinfo[$key] : array($dbinfo[$key]);
				}
			}
		}

		if (empty($entry))
		{
			++self::$success['unchanged'];
			return true;
		}

		$dn = "uid=" . $ldapinfo['uid'] . ",ou=users," . $hubLDAPBaseDN;

		// See if we're changing uid...if so, we need to do a rename
		if (array_key_exists('uid', $entry))
		{
			$result = ldap_rename($conn, $dn, 'uid='.$entry['uid'][0], 'ou=users,'.$hubLDAPBaseDN, true);

			// Set aside new uid and unset from attributes needing to be changed
			$newUid = $entry['uid'][0];
			unset($entry['uid']);

			// See if we have any items left
			if (empty($entry))
			{
				if ($result !== true)
				{
					self::$errors['warning'][] = ldap_error($conn);
					return false;
				}
				else
				{
					++self::$success['modified'];
					return true;
				}
			}

			// Build new dn
			$dn = "uid=" . $newUid . ",ou=users," . $hubLDAPBaseDN;
		}

		// Now do the modify
		$result = ldap_modify($conn, $dn, $entry);

		if ($result !== true)
		{
			self::$errors['warning'][] = ldap_error($conn);
			return false;
		}
		else
		{
			++self::$success['modified'];
			return true;
		}
	}

	/**
	 * Sync a group's info to LDAP
	 *
	 * @param   mixed   $group
	 * @return  boolean
	 */
	public static function syncGroup($group)
	{
		$db = \App::get('db');

		if (empty($db))
		{
			self::$errors['fatal'][] = 'Error connecting to the database';
			return false;
		}

		$conn = self::getLDO();

		if (empty($conn))
		{
			self::$errors['fatal'][] = 'LDAP connection failed';
			return false;
		}

		$query = "SELECT g.gidNumber, g.cn, g.description FROM #__xgroups AS g ";

		if (is_numeric($group) && ($group >= 0))
		{
			$query .= " WHERE g.gidNumber = " . $db->quote($group) . " LIMIT 1;";
		}
		else
		{
			$query .= " WHERE g.cn = " . $db->quote($group) . " LIMIT 1;";
		}

		$db->setQuery($query);
		$dbinfo = $db->loadAssoc();

		if (!empty($dbinfo))
		{
			$query = "SELECT DISTINCT(u.username) AS memberUid FROM #__xgroups_members AS gm, #__users AS u WHERE gm.gidNumber = " . $db->quote($dbinfo['gidNumber']) . " AND gm.uidNumber=u.id;";
			$db->setQuery($query);
			$dbinfo['memberUid'] = $db->loadColumn();
		}

		$ldap_params = \Component::params('com_system');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');

		if (isset($dbinfo['gidNumber']) || (is_numeric($group) && $group >= 0))
		{
			$dn = 'ou=groups,' . $hubLDAPBaseDN;
			$filter = '(gidNumber=' . ((isset($dbinfo['gidNumber'])) ? $dbinfo['gidNumber'] : $group) . ')';
		}
		else
		{
			$dn = "cn=" . $group . ",ou=groups," . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}

		$reqattr = array('gidNumber','cn','description','memberUid');

		$entry = ldap_search($conn, $dn, $filter, $reqattr, 0, 1, 0);
		$count = ($entry) ? ldap_count_entries($conn, $entry) : 0;

		// If there was a database entry, but there was no ldap entry, create the ldap entry
		if (!empty($dbinfo) && ($count <= 0))
		{
			$dn = "cn=" . $dbinfo['cn'] . ",ou=groups," . $hubLDAPBaseDN;

			$entry = array();
			$entry['objectclass'][] = 'top';
			$entry['objectclass'][] = 'posixGroup';

			foreach ($dbinfo as $key=>$value)
			{
				if (is_array($value) && $value != array())
				{
					$entry[$key] = $value;
				}
				else if (!is_array($value) && $value != '')
				{
					$entry[$key] = $value;
				}
			}

			$result = ldap_add($conn, $dn, $entry);

			if ($result !== true)
			{
				$result = ldap_add($conn, $dn, $entry);
				self::$errors['warning'][] = ldap_error($conn);
				return false;
			}
			else
			{
				++self::$success['added'];
				return true;
			}
		}

		$ldapinfo = null;

		$count = ($entry) ? ldap_count_entries($conn, $entry) : 0;

		if ($count > 0)
		{
			$firstentry = ldap_first_entry($conn, $entry);

			$attr = ldap_get_attributes($conn, $firstentry);

			if (!empty($attr) && $attr['count'] > 0)
			{
				foreach ($reqattr as $key)
				{
					unset($attr[$key]['count']);

					if (isset($attr[$key][0]))
					{
						if (count($attr[$key]) <= 1)
						{
							$ldapinfo[$key] = $attr[$key][0];
						}
						else
						{
							$ldapinfo[$key] = $attr[$key];
						}
					}
					else
					{
						$ldapinfo[$key] = null;
					}
				}
			}
		}

		// If there was no database entry, and there was no ldap entry, nothing to do
		if (empty($dbinfo) && empty($ldapinfo))
		{
			return true;
		}

		// If there was no database entry, but there was an ldap entry, delete the ldap entry
		if (!empty($ldapinfo) && empty($dbinfo))
		{
			$dn = "cn=" . $ldapinfo['cn'] . ",ou=groups," . $hubLDAPBaseDN;

			$result = ldap_delete($conn, $dn);

			if ($result !== true)
			{
				self::$errors['warning'][] = ldap_error($conn);
				return false;
			}
			else
			{
				++self::$success['deleted'];
				return true;
			}
		}

		// Otherwise update the ldap entry
		$entry = array();

		if (!empty($ldapinfo['memberUid']) && !is_array($ldapinfo['memberUid']))
		{
			$ldapinfo['memberUid'] = array($ldapinfo['memberUid']);
		}

		foreach ($dbinfo as $key=>$value)
		{
			if ($ldapinfo[$key] != $dbinfo[$key])
			{
				if ($dbinfo[$key] === null)
				{
					$entry[$key] = array();
				}
				else
				{
					$entry[$key] = $dbinfo[$key];
				}
			}
		}

		if (empty($entry))
		{
			++self::$success['unchanged'];
			return true;
		}

		$dn = "cn=" . $ldapinfo['cn'] . ",ou=groups," . $hubLDAPBaseDN;

		// See if we're changing cn...if so, we need to do a rename
		if (array_key_exists('cn', $entry))
		{
			$result = ldap_rename($conn, $dn, 'cn='.$entry['cn'], 'ou=groups,'.$hubLDAPBaseDN, true);

			// Set aside new uid and unset from attributes needing to be changed
			$newCn = $entry['cn'];
			unset($entry['cn']);

			// See if we have any items left
			if (empty($entry))
			{
				if ($result !== true)
				{
					self::$errors['warning'][] = ldap_error($conn);
					return false;
				}
				else
				{
					++self::$success['modified'];
					return true;
				}
			}

			// Build new dn
			$dn = "cn=" . $newCn . ",ou=groups," . $hubLDAPBaseDN;
		}

		// Now do the modify
		$result = ldap_modify($conn, $dn, $entry);

		if ($result !== true)
		{
			self::$errors['warning'][] = ldap_error($conn);
			return false;
		}
		else
		{
			++self::$success['modified'];
			return true;
		}
	}

	/**
	 * Add members to a group
	 *
	 * @param   mixed   $group
	 * @param   array   $members
	 * @return  boolean
	 */
	public static function addGroupMemberships($group, $members)
	{
		self::changeGroupMemberships($group, $members, array());
	}

	/**
	 * Remove members from a group
	 *
	 * @param   mixed   $group
	 * @param   array   $members
	 * @return  boolean
	 */
	public static function removeGroupMemberships($group, $members)
	{
		self::changeGroupMemberships($group, array(), $members);
	}

	/**
	 * Makes changes to a group
	 *
	 * @param   mixed   $group
	 * @param   array   $members
	 * @return  boolean
	 */
	public static function changeGroupMemberships($group,$add,$delete)
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$conn = self::getLDO();

		if (empty($conn))
		{
			return false;
		}

		$ldap_params = \Component::params('com_system');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');

		if (is_numeric($group) && $group >= 0)
		{
			$dn = 'ou=groups,' . $hubLDAPBaseDN;
			$filter = '(gidNumber=' . $group . ')';
		}
		else
		{
			$dn = "cn=$group,ou=groups," . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}

		$reqattr = array('gidNumber','cn');

		$entry = ldap_search($conn, $dn, $filter, $reqattr, 0, 1, 0);

		$count = ldap_count_entries($conn, $entry);

		// If there was a database entry, but there was no ldap entry, create the ldap entry
		if ($count <= 0)
		{
			return false;
		}

		$ldapinfo = null;

		if ($count > 0)
		{
			$firstentry = ldap_first_entry($conn, $entry);

			$attr = ldap_get_attributes($conn, $firstentry);

			if (!empty($attr) && $attr['count'] > 0)
			{
				foreach ($reqattr as $key)
				{
					unset($attr[$key]['count']);

					if (isset($attr[$key][0]))
					{
						if (count($attr[$key]) <= 2)
						{
							$ldapinfo[$key] = $attr[$key][0];
						}
						else
						{
							$ldapinfo[$key] = $attr[$key];
						}
					}
					else
					{
						$ldapinfo[$key] = null;
					}
				}
			}
		}

		if (empty($ldapinfo))
		{
			return false;
		}

		if (!empty($add))
		{
			$add = array_map( array($db, "Quote"), $add);
			$addin = implode(",", $add);

			if (!empty($addin))
			{
				$query = "SELECT username FROM #__users WHERE id IN ($addin) OR username IN ($addin);";
				$db->setQuery($query);
				$add = $db->loadColumn();
			}

			$adds = array();

			foreach ($add as $memberUid)
			{
				$adds['memberUid'][] = $memberUid;
			}

			if (ldap_mod_add($conn, $dn, $adds) == false)
			{
				// if bulk add fails, try individual
				foreach ($add as $memberUid)
				{
					ldap_mod_add($conn, $dn, array('memberUid' => $memberUid));
				}
			}
		}

		if (!empty($delete))
		{
			$delete = array_map( array($db, "Quote"), $delete);
			$deletein = implode(",", $delete);

			if (!empty($deletein))
			{
				$query = "SELECT username FROM #__users WHERE id IN ($deletein) OR username IN ($deletein);";
				$db->setQuery($query);
				$delete = $db->loadColumn();
			}

			$deletes = array();

			foreach ($delete as $memberUid)
			{
				$deletes['memberUid'][] = $memberUid;
			}

			ldap_mod_del($conn, $dn, $deletes);
		}
	}

	/**
	 * Sync all groups
	 *
	 * @return  boolean
	 */
	public static function syncAllGroups()
	{
		// @TODO: chunk this to 1000 groups at a time

		$db = \App::get('db');

		$query = "SELECT gidNumber FROM #__xgroups;";

		$db->setQuery($query);

		$result = $db->loadColumn();

		if ($result === false)
		{
			return false;
		}

		foreach ($result as $row)
		{
			self::syncGroup($row);

			if (is_array(self::$errors['fatal']) && !empty(self::$errors['fatal'][0]))
			{
				// If there's a fatal error, go ahead and stop
				return self::$errors;
			}
		}

		if (!empty(self::$errors['fatal'][0]) || !empty(self::$errors['warning'][0]))
		{
			return self::$errors;
		}
		else
		{
			return self::$success;
		}
	}

	/**
	 * Sync all users
	 *
	 * @return  boolean
	 */
	public static function syncAllUsers()
	{
		// @TODO: chunk this to 1000 users at a time

		$db = \App::get('db');

		$query = "SELECT id FROM #__users;";

		$db->setQuery($query);

		$result = $db->loadColumn();

		if ($result === false)
		{
			return false;
		}

		foreach ($result as $row)
		{
			self::syncUser($row);

			if (is_array(self::$errors['fatal']) && !empty(self::$errors['fatal'][0]))
			{
				// If there's a fatal error, go ahead and stop
				return self::$errors;
			}
		}

		if (!empty(self::$errors['fatal'][0]) || !empty(self::$errors['warning'][0]))
		{
			return self::$errors;
		}
		else
		{
			return self::$success;
		}
	}

	/**
	 * Remove all groups
	 *
	 * @return  array
	 */
	public static function deleteAllGroups()
	{
		$conn = self::getLDO();

		if (empty($conn))
		{
			self::$errors['fatal'][] = 'LDAP connection failed';
			return self::$errors;
		}

		// delete all old hubGroup schema based group entries
		$ldap_params = \Component::params('com_system');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');

		$dn = "ou=groups," . $hubLDAPBaseDN;
		$filter = '(objectclass=hubGroup)';

		$sr = ldap_search($conn, $dn, $filter, array('gid','cn'), 0, 0, 0);

		$gids = array();

		if ($sr !== false)
		{
			if (ldap_count_entries($conn, $sr) !== false)
			{
				$entry = ldap_first_entry($conn, $sr);

				while ($entry !== false)
				{
					$attr = ldap_get_attributes($conn, $entry);

					if (array_key_exists('gid', $attr))
					{
						$gids[] = "gid=" . $attr['gid'][0] . "," .  "ou=groups," . $hubLDAPBaseDN;
					}
					else if (array_key_exists('cn',$attr))
					{
						$gids[] = "cn=" . $attr['cn'][0] . "," .  "ou=groups," . $hubLDAPBaseDN;
					}

					$entry = ldap_next_entry($conn, $entry);
				}
			}
		}

		foreach ($gids as $giddn)
		{
			$result = ldap_delete($conn, $giddn);

			if ($result !== true)
			{
				self::$errors['warning'][] = ldap_error($conn);
			}
			else
			{
				++self::$success['deleted'];
			}
		}

		// delete all entries that have mysql counterparts
		// @TODO: chunk this to 1000 groups at a time

		$db = \App::get('db');

		$query = "SELECT cn FROM #__xgroups;";

		$db->setQuery($query);

		$result = $db->loadColumn();

		if ($result === false)
		{
			return false;
		}

		foreach ($result as $row)
		{
			$dn = "cn=$row," .  "ou=groups," . $hubLDAPBaseDN;

			// Added this search because the delete will error on a delete of a non existent object
			$sr = ldap_search($conn, "ou=groups," . $hubLDAPBaseDN, "cn=$row", array('cn'), 0, 0, 0);
			if (($sr !== false) and ldap_count_entries($conn, $sr) == 1)
			{
				$result = ldap_delete($conn, $dn);

				if ($result !== true)
				{
					// Don't report errors for "not such object" warnings
					if (ldap_errno($conn) != 32)
					{
						self::$errors['warning'][] = ldap_error($conn);
					}
				}
				else
				{
					++self::$success['deleted'];
				}
			}
		}

		// Delete any remaining items with gid > 1000
		$dn = "ou=groups," . $hubLDAPBaseDN;
		$filter = '(&(objectclass=posixGroup)(gidNumber>=1000))';

		$sr = ldap_search($conn, $dn, $filter, array('cn'), 0, 0, 0);

		$gids = array();

		if ($sr !== false)
		{
			if (ldap_count_entries($conn, $sr) !== false)
			{
				$entry = ldap_first_entry($conn, $sr);

				while ($entry !== false)
				{
					$attr = ldap_get_attributes($conn, $entry);

					$gids[] = $attr['cn'][0];

					$entry = ldap_next_entry($conn, $entry);
				}
			}
		}

		foreach ($gids as $gid)
		{
			$dn = "cn=$gid," . "ou=groups," . $hubLDAPBaseDN;
			$result = ldap_delete($conn, $dn);

			if ($result !== true)
			{
				self::$errors['warning'][] = ldap_error($conn);
			}
			else
			{
				++self::$success['deleted'];
			}
		}

		if (!empty(self::$errors['fatal'][0]) || !empty(self::$errors['warning'][0]))
		{
			return self::$errors;
		}
		else
		{
			return self::$success;
		}
	}

	/**
	 * Remove all users
	 *
	 * @return  array
	 */
	public static function deleteAllUsers()
	{
		$conn = self::getLDO();

		if (empty($conn))
		{
			self::$errors['fatal'][] = 'LDAP connection failed';
			return self::$errors;
		}

		// delete all old hubAccount schema based user entries
		$ldap_params = \Component::params('com_system');
		$hubLDAPBaseDN = $ldap_params->get('ldap_basedn','');

		$dn = "ou=users," . $hubLDAPBaseDN;
		$filter = '(objectclass=hubAccount)';

		$sr = ldap_search($conn, $dn, $filter, array('uid'), 0, 0, 0);

		$uids = array();

		if ($sr !== false)
		{
			if (ldap_count_entries($conn, $sr) !== false)
			{
				$entry = ldap_first_entry($conn, $sr);

				while ($entry !== false)
				{
					$attr = ldap_get_attributes($conn, $entry);

					$uids[] = $attr['uid'][0];

					$entry = ldap_next_entry($conn, $entry);
				}
			}
		}

		foreach ($uids as $uid)
		{
			$dn = "uid=$uid," . "ou=users," . $hubLDAPBaseDN;
			$result = ldap_delete($conn, $dn);

			if ($result !== true)
			{
				self::$errors['warning'][] = ldap_error($conn);
			}
			else
			{
				++self::$success['deleted'];
			}
		}

		// delete all entries that have mysql counterparts
		// @TODO: chunk this to 1000 groups at a time
		$db = \App::get('db');

		// Negative numbers exist as usernames for placeholders, these aren't in ldap
		// In fact we can't even search for them without causing an error
		$query = "SELECT username FROM `#__users` where (username not REGEXP '^-?\d+$' and username REGEXP '^[a-zA-Z]')";

		$db->setQuery($query);

		$result = $db->loadColumn();

		if ($result === false)
		{
			return false;
		}

		foreach ($result as $row)
		{
			$dn = "uid=$row," .  "ou=users," . $hubLDAPBaseDN;

			// see if item to be deleted is there
			$count = 0;
			$sr = ldap_search($conn, "ou=users," . $hubLDAPBaseDN, "uid=$row");

			if ($sr !== false)
			{
				if (ldap_count_entries($conn, $sr) !== false)
				{
					$count = ldap_count_entries($conn, $sr);
				}
			}

			if ($count > 0)
			{
				$result = ldap_delete($conn, $dn);
				if ($result !== true)
				{
					self::$errors['warning'][] = ldap_error($conn);
				}
				else
				{
					++self::$success['deleted'];
				}
			}
		}

		// delete any remaining items with gid > 1000
		$dn = "ou=users," . $hubLDAPBaseDN;
		$filter = '(&(objectclass=posixAccoiunt)(uidNumber>=1000))';

		$sr = ldap_search($conn, $dn, $filter, array('uid'), 0, 0, 0);

		$uids = array();

		if ($sr !== false)
		{
			if (ldap_count_entries($conn, $sr) !== false)
			{
				$entry = ldap_first_entry($conn, $sr);

				while ($entry !== false)
				{
					$attr = ldap_get_attributes($conn, $firstentry);

					$uids[] = $attr['uid'][0];

					$entry = ldap_next_entry($conn, $entry);
				}
			}
		}

		foreach ($uids as $uid)
		{
			$dn = "uid=$uid," . "ou=users," . $hubLDAPBaseDN;
			$result = ldap_delete($conn, $dn);

			if ($result !== true)
			{
				self::$errors['warning'][] = ldap_error($conn);
			}
			else
			{
				++self::$success['deleted'];
			}
		}

		if (!empty(self::$errors['fatal'][0]) || !empty(self::$errors['warning'][0]))
		{
			return self::$errors;
		}
		else
		{
			return self::$success;
		}
	}
}
