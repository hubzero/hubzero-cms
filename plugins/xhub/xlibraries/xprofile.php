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

class XProfileHelper
{
	public function iterate_profiles($func, $storage)
	{
		$db = &JFactory::getDBO();

		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
			return false;

		if ($storage == 'ldap')
		{
			$xhub = &XFactory::getHub();
			$conn = &XFactory::getPLDC();

			$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

			$dn = 'ou=users,' . $hubLDAPBaseDN;
			$filter = '(objectclass=posixAccount)'; 

			$attributes[] = 'uid';

			$sr = @ldap_search($conn, $dn, $filter, $attributes, 0, 0, 0);

			if ($sr === false)
				return false;

			$count = @ldap_count_entries($conn, $sr);

			if ($count === false)
				return false;

			$entry = @ldap_first_entry($conn, $sr);

			do
			{
				$attributes = ldap_get_attributes($conn, $entry);
				$func($attributes['uid'][0]);
				$entry = @ldap_next_entry($conn, $entry);
			}
			while($entry !== false);
		}

		if ($storage == 'mysql')
		{
			$query = "SELECT uidNumber FROM #__xprofiles;";

			$db->setQuery($query);

			$result = $db->query();

			if ($result === false)
			{
				$this->setError('Error retrieving data from xprofiles table: ' . $db->getErrorMsg());
				return false;
			}

			while ($row = mysql_fetch_row( $result )) 
				$func($row[0]);

			mysql_free_result( $result );
		}

		return true;
	}

	public function delete_profile($user, $storage)
	{
		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
			return false;

		$mconfig = & JComponentHelper::getParams( 'com_members' );
		$ldapProfileMirror = $mconfig->get('ldapProfileMirror');

		if (empty($storage))
			$storage = ($ldapProfileMirror) ? 'all' : 'mysql';
		
		$profile = new XProfile();

		if ($storage == 'mysql' || $storage == 'all')
		{
			$profile->load($user,'mysql');
			$profile->delete('mysql');
		}

		if ($storage == 'ldap' || $storage == 'all')
		{
			$profile->load($user,'ldap');
			$profile->delete('ldap');
		}
	}
	
	public function find_by_email($email)
	{
		if (empty($email))
			return false;

		$db = &JFactory::getDBO();
		
		$query = "SELECT username FROM #__xprofiles WHERE email=" . $db->Quote($email);
		
		$db->setQuery($query);

		$result = $db->loadResultArray();
		
		if (empty($result))
			return false;
			
		return $result;
	}
}


class XProfile extends JObject
{
	// properties
	private $uidNumber = null;
	private $name = null;
	private $username = null;
	private $email = null;
	private $registerDate = null;
	private $gidNumber = null;
	private $homeDirectory = null;
	private $loginShell = null;
	private $ftpShell = null;
	private $userPassword = null;
	private $shadowExpire = null;
	private $gid = null;
	private $orgtype = null;
	private $organization = null;
	private $countryresident = null;
	private $countryorigin = null;
	private $gender = null;
	private $url = null;
	private $reason = null;
	private $mailPreferenceOption = null;
	private $usageAgreement = null;
	private $jobsAllowed = null;
	private $modifiedDate = null;
	private $emailConfirmed = null;
	private $regIP = null;
	private $regHost = null;
	private $nativeTribe = null;
	private $phone = null;
	private $proxyPassword = null;
	private $proxyUidNumber = null;
	private $givenName = null;
	private $middleName = null;
	private $surname = null;
	private $picture = null;
	private $vip = null;
	private $public = null;
	private $params = null;
	private $note = null;
	// properties stored in auxilliary tables
	private $_auxs_bio = null;
	// multi-value properties stored in auxilliary tables
	private $_auxv_disability = array();
	private $_auxv_hispanic = array();
	private $_auxv_race = array();
	private $_auxv_admin = array();
	private $_auxv_host = array();
	private $_auxv_manager = array();
	private $_auxv_edulevel = array();
	private $_auxv_role = array();
	//private $_auxv_tag = array();
	// private class variables
	private $_password = null;
	private $_params = null;

	// static class variables
	static $_s_propertyattrmap = array('username' => 'uid', 'name' => 'cn', 'uidNumber' => 'uidNumber',
			'gidNumber' => 'gidNumber', 'homeDirectory' => 'homeDirectory', 'email' => 'mail', 
			'registerDate' => 'regDate', 'loginShell' => 'loginShell', 'ftpShell' => 'ftpShell', 
			'userPassword' => 'userPassword', 'gid' => 'gid', 'orgtype' => 'orgtype', 
			'organization' => 'o', 'countryresident' => 'countryresident', 'countryorigin' => 'countryorigin',
			'gender' => 'sex', 'url' => 'url', 'reason' => 'description', 
			'mailPreferenceOption' => 'mailPreferenceOption', 'usageAgreement' => 'usageAgreement', 
			'jobsAllowed' => 'jobsAllowed', 'modifiedDate' => 'modDate', 'emailConfirmed' => 'emailConfirmed', 
			'regIP' => 'regIP', 'regHost' => 'regHost', 'nativeTribe' => 'nativeTribe', 'phone' => 'homePhone',
			'proxyUidNumber' => 'proxyUidNumber', 'proxyPassword' => 'proxyPassword', 'disability' => 'disability', 
			'hispanic' => 'hispanic', 'race' => 'race', 'admin' => 'admin', 'host' => 'host', 'edulevel' => 'edulevel',
			'role' => 'role', 'shadowExpire' => 'shadowExpire');

	public function setError($msg)
	{
		$bt = debug_backtrace();

		$error = "XProfile::" . $bt[1]['function'] . "():" . $msg;

		array_push($this->_errors, $error);
	}

	private function logDebug($msg)
	{
		$xlog =& XFactory::getLogger();
		$xlog->logDebug($msg);

		return true;
	}

	public function clear()
	{
		$classvars = get_class_vars('XProfile');

		foreach ($classvars as $property => $value)
		{
			if ('_s_' == substr($property, 0, 3)) // don't touch static variables
				continue;
			
			unset($this->$property);
			$this->$property = $value;
		}

		$objvars = get_object_vars($this);

		foreach ($objvars as $property => $value)
		{
			if (!array_key_exists($property, $classvars))
				unset($this->$property);
		}

		return true;
	}

	private function _ldap_get_user($username = null)
	{
		$xhub = &XFactory::getHub();
		$conn = &XFactory::getPLDC();

		if ( empty($username))
		{
			$this->setError("missing username");
			return false;
		}

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (empty($hubLDAPBaseDN))
		{
			$this->setError("hubLDAPBaseDN variable not configured");
			return false;
		}

		if (!is_numeric($username)) 
		{
			$dn = "uid=$username,ou=users," . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}
		else
		{
			$dn = 'ou=users,' . $hubLDAPBaseDN;
			$filter = '(uidNumber=' . $username . ')';
		}

		$attributes[] = 'sn';
		$attributes[] = 'member';

		foreach(XProfile::$_s_propertyattrmap as $property => $attribute)
			$attributes[] = $attribute;

		$sr = @ldap_search($conn, $dn, $filter, $attributes, 0, 1, 0, 3);

		if ($sr === false)
		{
			$this->setError("ldap_search() failed: " . ldap_error($conn));
			return false;
		}

		$count = @ldap_count_entries($conn, $sr);

		if ($count === false)
		{
			$this->setError("ldap_count_entries() failed: " . ldap_error($conn));
			return false;
		}

		if ($count != 1)
		{
			$this->setError("user not found");
			return false;
		}

		$entry = @ldap_first_entry($conn, $sr);

		if ($entry === false)
		{
			$this->setError("ldap_first_entry() failed: " . ldap_error($conn));
			return false;
		}

		$attributes = ldap_get_attributes($conn, $entry);

		if ($attributes === false)
		{
			$this->setError("ldap_get_attributes() failed: " . ldap_error($conn));
			return false;
		}

		$userinfo = array();

		foreach(XProfile::$_s_propertyattrmap as $property => $attribute)
		{
			if (!isset($attributes[$attribute][0]))
				$userinfo[$attribute] = false;
			else
			{
				if ($attributes[$attribute]['count'] > 1)
				{
					unset($attributes[$attribute]['count']);
					$userinfo[$attribute] = $attributes[$attribute];
					sort($userinfo[$attribute]);
				}
				else
				{
					unset($attributes[$attribute]['count']);

					if (property_exists('XProfile','_auxv_' . $property))
						$userinfo[$attribute] = $attributes[$attribute];
					else
						$userinfo[$attribute] = $attributes[$attribute][0];
				}
			}
		}

		$userinfo['sn'] = isset($attributes['sn'][0]) ? $attributes['sn'][0] : false;

		if (isset($attributes['member']) && $attributes['member']['count'] > 0)
		{
			unset($attributes['member']['count']);
			$userinfo['member'] = $attributes['member'];
		}
		else
			$userinfo['member'] = false;

		return $userinfo;
	}

	private function _ldap_load($username = null)
	{
		$userinfo = $this->_ldap_get_user($username);
		
		if ($userinfo == false)
			return false;

		$this->clear();

		foreach(XProfile::$_s_propertyattrmap as $property => $attribute)
		{
			if ($property == 'usageAgreement')
				$this->set($property, $userinfo[$attribute] == 'TRUE' ? '1' : '0');
			else if ($property == 'modDate' && empty($userinfo[$attribute]))
				$this->set($property, '0000-00-00 00:00:00');
			else
				$this->set($property, $userinfo[$attribute]);
		}

		return true;
	}

	private function _mysql_load($user)
	{
		$db = &JFactory::getDBO();

		if (empty($user))
		{
			$this->setError('No user specified');
			return false;
		}

		if (is_numeric($user))
			$query = "SELECT * FROM #__xprofiles WHERE uidNumber = " . $db->Quote(intval($user)) . ";";
		else
			$query = "SELECT * FROM #__xprofiles WHERE username = " . $db->Quote($user) . " AND uidNumber>0;";

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if ($result === false)
		{
			$this->setError('Error retrieving data from xprofiles table: ' . $db->getErrorMsg());
			return false;
		}

		if (empty($result))
		{
			$this->setError('No such user [' . $user . ']');
			return false;
		}

		$this->clear();

		$this->_params = new JParameter( '' );

		foreach($result as $property=>$value)
			$this->set($property,$value);

		$classvars = get_class_vars('XProfile');

		foreach ($classvars as $property => $value)
		{
			if ('_auxv_' == substr($property, 0, 6) || '_auxs_' == substr($property, 0, 6))
				$this->$property = false; // this property is loaded on demand
		}

		$this->_params->loadINI($this->params);

		return true;
	}

	private function _mysql_author_load($authorid)
	{
		static $_propertyauthormap = array('uidNumber' => 'id', 'givenName' => 'firstname', 'middleName' => 'middlename',
			'surname' => 'lastname', 'organization' => 'org', 'bio' => 'bio', 'url' => 'url', 'picture' => 'picture',
			'vip' => 'principal_investigator');
		
		$db = & JFactory::getDBO();
		$xhub = &XFactory::getHub();

		$query = "SELECT * FROM #__author WHERE id=" . $db->Quote($authorid);

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if ($result === false)
		{
			$this->setError('Error retrieving data from author table: ' . $db->getErrorMsg());
			return false;
		}

		if (empty($result))
		{
			$this->setError('No such author [' . $authorid . ']');
			return false;
		}

		$this->clear();

		foreach($_propertyauthormap as $property => $aproperty)
			if (!empty($result[$aproperty]))
				$this->set($property, $result[$aproperty]);

		return true;
	}

	private function _xregistration_load($registration)
	{
		static $_propertyregmap = array('username' => 'login', 'name' => 'name', 'email' => 'email', 'orgtype' => 'orgtype',
			'organization' => 'org', 'countryresident' => 'countryresident', 'countryorigin' => 'countryorigin',
			'gender' => 'sex', 'url' => 'web', 'reason' => 'reason', 'mailPreferenceOption' => 'mailPreferenceOption',
			'usageAgreement' => 'usageAgreement', 'nativeTribe' => 'nativeTribe', 'phone' => 'phone', 
			'disability' => 'disability', 'hispanic' => 'hispanic', 'race' => 'race', 'admin' => 'admin', 
			'host' => 'host', 'edulevel' => 'edulevel', 'role' => 'role');
		
		if (!is_object($registration))
		{
			$this->setError("Invalid XRegistration object");
			return false;
		}

		foreach($_propertyregmap as $property => $rproperty)
			if ($registration->get($rproperty) !== null)
				$this->set($property, $registration->get($rproperty));
		
		$this->set('mailPreferenceOption', $this->get('mailPreferenceOption') ? '2' : '0');
		$this->set('usageAgreement', $this->get('usageAgreement') ? '1' : '0');
	
		return true;
	}

	public function load($user, $storage = 'mysql')
	{
		if (!empty($storage) && !in_array($storage,array('mysql','ldap','author','xregistration')))
		{
			$this->setError('Invalid storage option requested [' . $storage . ']');
			return false;
		}

		if ($storage == 'mysql')
			return $this->_mysql_load($user);

		if ($storage == 'ldap')
			return $this->_ldap_load($user);

		if ($storage == 'author')
			return $this->_mysql_load_author($user);

		if ($storage == 'xregistration')
			return $this->_xregistration_load($user);

		return true;
	}

	public function __construct($user = null)
	{
		if (!empty($user))
			return $this->load($user);

		return true;
	}

	private function _ldap_create()
	{
		if (!is_numeric($this->get('uidNumber')))
		{
			$this->setError("missing required field 'uidNumber'");
			return false;
		}

		if ($this->get('username') == '')
		{
			$this->setError("missing required field 'username'");
			return false;
		}

		if (!is_numeric($this->get('gidNumber')))
		{
			$this->setError("missing required field 'gidNumber'");
			return false;
		}

		if ($this->get('homeDirectory') == '')
		{
			$this->setError("missing required field 'homeDirectory'");
			return false;
		}

		if ($this->get('name') == '')
		{
			$this->setError("missing required field 'name'");
			return false;
		}

		if ($this->get('gid') == '')
		{
			$this->setError("missing required field 'gid'");
			return false;
		}

		$xhub = &XFactory::getHub();
		$conn = &XFactory::getPLDC();

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (empty($hubLDAPBaseDN))
		{
			$this->setError("hubLDAPBaseDN variable not configured");
			return false;
		}

		$dn = 'uid=' . $this->get('username') . ',ou=users,' . $hubLDAPBaseDN;
		
		$entry = array();
		$entry['objectclass'][] = 'top';
		$entry['objectclass'][] = 'person';
		$entry['objectclass'][] = 'organizationalPerson';
		$entry['objectclass'][] = 'inetOrgPerson';
		$entry['objectclass'][] = 'posixAccount';
		$entry['objectclass'][] = 'shadowAccount';
		$entry['objectclass'][] = 'hubAccount';

		$entry['sn'] = $this->get('name');

		foreach(XProfile::$_s_propertyattrmap as $property => $attribute)
		{
			$value = $this->get($property);

			if (is_array($value) && $value != array())
				$entry[$attribute] = $value;
			else if (!is_array($value) && $value != '')
				$entry[$attribute] = $value;
		}

		if (isset($entry['usageAgreement']))
			$entry['usageAgreement'] = $entry['usageAgreement'] ? 'TRUE' : 'FALSE';

		if (!@ldap_add($conn, $dn, $entry))
		{
			$this->setError("ldap_add() failed: " . ldap_error($conn));
			return false;
		}

		return true;
	}

	private function _mysql_create()
	{
		$db = &JFactory::getDBO();
		$xhub = &XFactory::getHub();

		$modifiedDate = gmdate('Y-m-d H:i:s');

		if (is_numeric($this->get('uidNumber')))
		{
			$query = "INSERT INTO #__xprofiles (uidNumber,username,modifiedDate) VALUE ("
				. $db->Quote($this->get('uidNumber')) . ','
				. $db->Quote($this->get('username')) . ',' 
				. $db->Quote($modifiedDate) . ");";

			$db->setQuery( $query );

			if (!$db->query())
			{
				$errno = $db->getErrorNum();

				if ($errno == 1062)
					$this->setError('uidNumber (' . $this->get('uidNumber') 
						. ') already exists'
						. ' in xprofiles table');
				else
					$this->setError('Error inserting user data to xprofiles table: ' 
						. $db->getErrorMsg());
			
				return false;
			}
		}
		else
		{
			$token = uniqid();

			$query = "INSERT INTO #__xprofiles (uidNumber,username,modifiedDate) SELECT "
				. "IF(MIN(uidNumber)>0,-1,MIN(uidNumber)-1)," 
				. $db->Quote($token) . ',' . $db->Quote($modifiedDate) . " FROM #__xprofiles;";

			$db->setQuery( $query );

			if (!$db->query())
			{
				$this->setError('Error inserting non-user data to xprofiles table: ' 
					. $db->getErrorMsg());
	
				return false;
			}

			$query = "SELECT uidNumber from #__xprofiles WHERE username=" . $db->Quote($token)
				. " AND modifiedDate=" . $db->Quote($modifiedDate);

			$db->setQuery($query);

 			$result = $db->loadResultArray();

			if ($result === false)
			{
				$this->setError('Error adding data to xprofiles table: ' 
					. $db->getErrorMsg());

				return false;
			}

			if (count($result) > 1)
			{
				$this->setError('Error adding data to xprofiles table: ' 
					. $db->getErrorMsg());

				return false;
			}

			$this->set('uidNumber', $result[0]);
		}

		if ($this->_mysql_update() === false)
			return false;

		return true;
	}

	public function create($storage = null)
	{
		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
		{
			$this->setError('Invalid storage option requested [' . $storage . ']');
			return false;
		}

		$mconfig = & JComponentHelper::getParams( 'com_members' );
		$ldapProfileMirror = $mconfig->get('ldapProfileMirror');
		
		$modifiedDate = gmdate('Y-m-d H:i:s');
		$this->set('modifiedDate', $modifiedDate);

		if (empty($storage))
			$storage = ($ldapProfileMirror) ? 'all' : 'mysql';

		if ($storage == 'mysql' || $storage == 'all')
			if ($this->_mysql_create() === false)
				return false;

		if (($storage == 'ldap' || $storage == 'all'))
			if ($this->_ldap_create() === false)
				return false;

		return true;
	}

	private function _ldap_update()
	{
		$xhub = &XFactory::getHub();
		$conn = &XFactory::getPLDC();
		$errno = 0;

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (empty($hubLDAPBaseDN))
		{
			$this->setError("hubLDAPBaseDN variable not configured");
			return false;
		}

		$userinfo = $this->_ldap_get_user( $this->get('username') );

		if ($userinfo === false)
			return false;
		
		$dn = 'uid=' . $this->get('username') . ',ou=users,' . $hubLDAPBaseDN;

		$replace_attr = array();
		$add_attr = array();
		$delete_attr = array();

		if ($userinfo['uid'] !== false && $this->get('username') == '')
		{
			$this->setError("can't delete required attribute 'uid'");
			return false;
		}

		if ($userinfo['uid'] !== false && $this->get('username') != $userinfo['uid'])
		{
			$this->setError("changing uid attribute is currently not supported");
			return false;
		}


		if ($userinfo['uid'] === false && $this->get('username') != '')
		{
			$this->setError("can't add missing required attribute 'uid'");
			return false;
		}

		if ($userinfo['userPassword'] !== false && $this->get('userPassword') == '')
		{
			$this->setError("can't delete required attribute 'userPassword'");
			return false;
		}

		if ($userinfo['cn'] !== false && $this->get('name') == '')
		{
			$this->setError("can't delete required attribute 'cn'");
			return false;
		}

		if ($userinfo['homeDirectory'] !== false && $this->get('homeDirectory') == '')
		{
			$this->setError("can't delete required attribute 'homeDirectory'");
			return false;
		}

		if ($userinfo['usageAgreement'] == 'TRUE')
			$userinfo['usageAgreement'] = '1';
		elseif ($userinfo['usageAgreement'] == 'FALSE')
			$userinfo['usageAgreement'] = '0';

		foreach(XProfile::$_s_propertyattrmap as $property => $attribute)
		{
			$current = $this->get($property);

			if ($current == array() || $current === null) 
				$current = '';

			if ($userinfo[$attribute] !== false && $current == '')
				$delete_attr[$attribute] = array();
			elseif ($userinfo[$attribute] !== false && $current != $userinfo[$attribute])
				$replace_attr[$attribute] = $current;
			elseif ($userinfo[$attribute] === false && $current != '')
				$add_attr[$attribute] = $current;
		}

		if ($userinfo['sn'] !== false && $this->get('name') != '')
			$replace_attr['sn'] = $this->get('name');
		elseif ($userinfo['sn'] === false && $this->get('name') != '')
			$add_attr['sn'] = $this->get('name');
			
		if (isset($replace_attr['usageAgreement']))
			$replace_attr['usageAgreement'] = $replace_attr['usageAgreement'] ? 'TRUE' : 'FALSE';
		if (isset($add_attr['usageAgreement']))
			$add_attr['usageAgreement'] = $add_attr['usageAgreement'] ? 'TRUE' : 'FALSE';

		if (!@ldap_mod_replace($conn, $dn, $replace_attr))
		{
			$this->setError("ldap_mod_replace() failed: " . ldap_error($conn));
			$errno = @ldap_errno($conn);
		}

		if (!@ldap_mod_add($conn, $dn, $add_attr))
		{
			$this->setError("ldap_mod_add() failed: " . ldap_error($conn));
			$errno = @ldap_errno($conn);
		}

		if (!@ldap_mod_del($conn, $dn, $delete_attr))
		{
			$this->setError("ldap_mod_del() failed: " . ldap_error($conn));
			$errno = @ldap_errno($conn);
		}

		if ($errno != 0)
			return false;
		
		return true;
	}

	private function _mysql_update_auxilliary_tables()
	{
		$db = &JFactory::getDBO();

		$classvars = get_class_vars('XProfile');

		foreach ($classvars as $property => $value)
		{
			if ( ('_auxv_' != substr($property,0,6)) && ('_auxs_' != substr($property,0,6)) )
				continue;

			$property = substr($property,6);
			$first = true;
			$query = "REPLACE INTO #__xprofiles_" . $property . " (uidNumber, " . $property . ") VALUES ";
			
			$list = $this->get($property);

			if (!empty($list))
			{
				if (!is_array($list))
					$list = array($list);

				foreach($list as $value)
				{
					if (!$first)
						$query .= ',';

					$first = false;
					$query .= '(' . $db->Quote($this->get('uidNumber')) . ',' . $db->Quote($value) . ')';
				}

				$db->setQuery( $query );

				if (!$db->query())
				{
					$this->setError("Error updating data in xprofiles $property table: "
						. $db->getErrorMsg());

					return false;
				}
			}

			if (property_exists('XProfile', '_auxv_'.$property))
			{
			    	foreach($list as $key=>$value)
				    $list[$key] = $db->Quote( $value );

				$valuelist = implode($list,",");

				if (empty($valuelist))
				    	$valuelist = "''";

				$query = "DELETE FROM #__xprofiles_" . $property . " WHERE uidNumber=" 
					. $this->get('uidNumber') . " AND $property NOT IN ($valuelist);";
				
				$db->setQuery( $query );

				if (!$db->query())
				{
					$this->setError("Error deleting data in xprofiles $property table: "
						. $db->getErrorMsg());

					return false;
				}
			}
		}

		return true;

	}

	private function _mysql_update($mysqlonly = false)
	{
		$db = &JFactory::getDBO();

		$query = "UPDATE #__xprofiles SET ";

		$classvars = get_class_vars('XProfile');
		$first = true;

		foreach ($classvars as $property => $value)
		{
			if ('_' == substr($property, 0, 1))
				continue;

			if (!$first)
				$query .= ',';
			else
				$first = false;

			if ($this->get($property) == null)
			    $query .= "$property=NULL";
			else
				$query .= "$property=" . $db->Quote($this->get($property));
		}
 
		$query .= " WHERE uidNumber=" . $db->Quote($this->get('uidNumber')) . ";";

		$db->setQuery( $query );

		if (!$db->query())
		{
			$this->setError('Error updating data in xprofiles table: ' . $db->getErrorMsg());
			return false;
		}

		if ($this->_mysql_update_auxilliary_tables() === false)
			return false;

		return true;
	}

	public function update($storage = null)
	{
		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
		{
			$this->setError('Invalid storage option requested [' . $storage . ']');
			return false;
		}

		$mconfig = & JComponentHelper::getParams( 'com_members' );
		$ldapProfileMirror = $mconfig->get('ldapProfileMirror');

		$params = $this->_params;
		$this->params = (is_object($params)) ? $params->toString() : '';

		$modifiedDate = gmdate('Y-m-d H:i:s');
		$this->set('modifiedDate', $modifiedDate);

		if (empty($storage))
			$storage = ($ldapProfileMirror) ? 'all' : 'mysql';

		if ($storage == 'mysql' || $storage == 'all')
			if ($this->_mysql_update() === false)
				return false;

		if (($storage == 'ldap' || $storage == 'all'))
			if ($this->_ldap_update() === false)
				return false;

		return true;
	}

	public function store($updateOnly = false, $storage = null)
	{
		$db = &JFactory::getDBO();

		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
		{
			$this->setError('Invalid storage option requested [' . $storage . ']');
			return false;
		}

		$mconfig = & JComponentHelper::getParams( 'com_members' );
		$ldapProfileMirror = $mconfig->get('ldapProfileMirror');

		$modifiedDate = gmdate('Y-m-d H:i:s');
		$this->set('modifiedDate', $modifiedDate);

		if (empty($storage))
			$storage = ($ldapProfileMirror) ? 'all' : 'mysql';

		if ($updateOnly && $this->get('uidNumber') == '')
		{
			$this->setError('No uidNumber property set for updateOnly action');
			return false;
		}

		if ($storage == 'all' || $storage == 'mysql')
		{
			$mysql_insert = false;

			if (!$updateOnly)
			{
				$query = "SELECT uidNumber FROM #__xprofiles WHERE uidNumber=" . 
					$db->Quote($this->get('uidNumber'));

				$db->setQuery($query);

				if (!$db->query())
				{
					$this->setError("Error retrieving data from xprofiles table: " 
						. $db->getErrorMsg());

					return false;
				}

				$result = $db->loadResult();
				$mysql_insert = empty($result);
			}

			if ($mysql_insert == true)
			{
				if ($this->_mysql_create() === false)
					return false;
			}
			else if ($this->_mysql_update() === false)
					return false;
		}

		if ($storage == 'all' || $storage == 'ldap')
		{
			$userinfo = $this->_ldap_get_user($this->get('uidNumber'));

			if ($userinfo === false)
			{
				if ($this->_ldap_create() === false)
					return false;
			}
			else if ($this->_ldap_update() === false)
				return false;
		}

		return true;
	}

	static function getInstance($user = null)
	{
		$instance = new XProfile($user);

		if ($instance->get('uidNumber') == '')
			return false;

		return $instance;
	}

	private function _get_auxilliary_property($property)
	{
		$db = & JFactory::getDBO();

		if ($this->$property !== false)
			return $this->$property;

		$property_name = substr($property,6);

		$query = "SELECT $property_name FROM #__xprofiles AS x,#__xprofiles_$property_name AS xp WHERE "
				. "x.uidNumber=xp.uidNumber AND xp.uidNumber=" . $db->Quote($this->get('uidNumber')) 
				. " ORDER BY $property_name ASC;";

		$db->setQuery($query);
		$result = $db->loadResultArray();

		if ($result === false)
		{
			$this->setError("Error retrieving data from xprofiles $property table: " 
				. $db->getErrorMsg());

			return false;
		}

		if ('_auxs_' == substr($property,0,6)) {
			if (isset($result[0])) {
				$this->set($property_name, $result[0]);
			} else {
				$this->set($property_name, '');
			}
		} else {
			if (is_array($result) && count($result) <= 1 && empty($result[0]))
				 $this->set($property_name, array());
			else
				$this->set($property_name, $result);
		}
		return $result;
	}

	public function get($property)
	{
		if ($property == 'password')
			return $this->_password;

		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (!property_exists('XProfile',$property)) 
		{
			if (property_exists('XProfile','_auxs_' . $property))
				$property = '_auxs_' . $property;
			else if (property_exists('XProfile','_auxv_' . $property))
				$property = '_auxv_' . $property;
			else
			{
				$this->setError("Unknown property: $property");
				return false;
			}
		}

		if ($this->$property === false)
			$this->_get_auxilliary_property($property);

		return $this->$property;
	}

	public function set($property,$value) 
	{
		if ($property == 'password')
		{
			if ($value != '')
				$this->userPassword = "{MD5}" . base64_encode(pack('H*', md5($value)));
			else
				$this->userPassword = '';

			$this->_password = $value;

			return true;
		}

		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (!property_exists('XProfile', $property))
		{
			if (property_exists('XProfile','_auxs_' . $property))
				$property = '_auxs_' . $property;
			else if (property_exists('XProfile','_auxv_' . $property))
				$property = '_auxv_' . $property;
			else
			{
				$this->setError("Unknown property: $property");
				return false;
			}
		}

		if ('_auxv_' == substr($property, 0, 6))
		{
			if (empty($value))
				$value = array();
			else
			{
				if (!is_array($value))
					$value = array($value);

				$list = array_unique($value);
				sort($list);
				unset($value);
				foreach($list as $v)
					$value[] = strval($v);
			}
		}
		else
			$value = strval($value);

		$this->$property = $value;
		
		if ($property == 'userPassword')
			$this->_password = '';

		return true;
	}

	public function add($property, $value)
	{
		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (property_exists('XProfile',$property) || property_exists('XProfile','_auxs_' . $property))
		{
			$this->setError("Can't add value(s) to non-array property.");
			return false;
		}

		if (!property_exists('XProfile','_auxv_' . $property))
		{
			$this->setError("Unknown property: $property");
			return false;
		}

		if (empty($value))
			return true;

		if (!is_array($value))
			$value = array($value);

		$property = '_auxv_' . $property;

		foreach($value as $v)
		{
			$v = strval($v);

			if (!in_array($v, $this->$property))
				array_push($this->$property, $v);
		}

		sort($this->$property);

		return true;
	}

	public function remove($property, $value)
	{
		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (property_exists('XProfile',$property) || property_exists('XProfile','_auxs_' . $property))
		{
			$this->setError("Can't remove value(s) from non-array property.");
			return false;
		}

		if (!property_exists('XProfile', '_auxv_' . $property))
		{
			$this->setError("Unknown property: $property");
			return false;
		}

		if (!isset($value))
			return true;

		if (!is_array($value))
			$value = array($value);

		$property = '_auxv_' . $property;

		foreach($value as $v)
		{
			$v = strval($v);

			if (in_array($v, $this->$property))
				$this->$property = array_diff($this->$property, array($v));
		}

		return true;
	}

        function getParam( $key, $default = null )
        {
                return $this->_params->get( $key, $default );
        }

        function setParam( $key, $value )
        {
                return $this->_params->set( $key, $value );
        }

        function defParam( $key, $value )
        {
                return $this->_params->def( $key, $value );
        }

        function &getParameters($loadsetupfile = false, $path = null)
        {
                static $parampath;

		/*

                // Set a custom parampath if defined
                if( isset($path) ) {
                        $parampath = $path;
                }

                // Set the default parampath if not set already
                if( !isset($parampath) ) {
                        $parampath = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_members'.DS.'models';
                }

                if($loadsetupfile)
                {
                        $type = str_replace(' ', '_', strtolower($this->usertype));

                        $file = $parampath.DS.$type.'.xml';
                        if(!file_exists($file)) {
                                $file = $parampath.DS.'user.xml';
                        }

                        $this->_params->loadSetupFile($file);
                }

		*/

                return $this->_params;
        }

        function setParameters($params )
        {
                $this->_params = $params;
        }

	private function _ldap_delete()
	{
		$xhub =& XFactory::getHub();
		$conn =& XFactory::getPLDC();

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (empty($hubLDAPBaseDN))
		{
			$this->setError("hubLDAPBaseDN variable not configured");
			return false;
		}

		$userinfo = $this->_ldap_get_user($this->get('username'));

		if ($userinfo === false)
			return false;
		
		if (empty($userinfo['uid']))
		{
			$this->setError("missing required field 'uidNumber'");
			return false;
		}
		
		$dn = "uid=" . $userinfo['uid'] . ",ou=users," . $hubLDAPBaseDN;
		
		if (!@ldap_delete($conn, $dn))
		{
			$this->setError("ldap_delete() failed: " . ldap_error());
			return false;
		}
		
		return true;
	}

	private function _mysql_delete()
	{
		$db = & JFactory::getDBO();
		
		if (!is_numeric($this->get('uidNumber')))
		{
			$this->setError("missing required field 'uidNumber'");
			return false;
		}

		$classvars = get_class_vars('XProfile');

		foreach ($classvars as $property => $value)
		{
			if ('_auxv_' != substr($property, 0, 6) && '_auxs_' != substr($property, 0, 6))
				continue;

			$property = substr($property, 6);
			
			$query = "DELETE FROM #__xprofiles_$property WHERE uidNumber = '" . $this->get('uidNumber') . "'";
			$db->setQuery( $query );

			if (!$db->query())
			{
				$this->setError("Error deleting from xprofiles $property table: "
					. $db->getErrorMsg());
				return false;
			}
		}

		$query = "DELETE FROM #__xprofiles WHERE uidNumber = '" . $this->get('uidNumber') . "'";
		$db->setQuery( $query );

		if (!$db->query())
		{
			$this->setError("Error deleting from xprofiles table: " . $db->getErrorMsg());
			return false;
		}

		$this->clear();

		return true;
	}

	public function delete($storage = null)
	{
		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
		{
			$this->setError('Invalid storage option requested [' . $storage . ']');
			return false;
		}

		$mconfig = & JComponentHelper::getParams( 'com_members' );
		$ldapProfileMirror = $mconfig->get('ldapProfileMirror');

		if (empty($storage))
			$storage = ($ldapProfileMirror) ? 'all' : 'mysql';

		if ($storage == 'mysql' || $storage == 'all')
			if ($this->_mysql_delete() === false)
				return false;

		if ($storage == 'ldap' || $storage == 'all')
			if ($this->_ldap_delete() === false)
				return false;

		return true;
	}

        function hasTransientUsername()
        {
                $parts = explode(':', $this->get('username'));

                if ( count($parts) == 3 && intval($parts[0]) < 0 )
                        return true;
        }

        function getTransientUsername()
        {
                $parts = explode(':', $this->get('username'));

                if ( count($parts) == 3 && intval($parts[0]) < 0 )
                        return pack("H*", $parts[1]);
        }

        function hasTransientEmail()
        {
                if (eregi( "\.localhost\.invalid$", $this->get('email')))
                        return true;
        }

        function getTransientEmail()
        {
                if (eregi( "\.localhost\.invalid$", $this->get('email')))
                {
                        $parts = explode('@', $this->get('email'));
                        $parts = explode('-', $parts[0]);
                        return pack("H*", $parts[2]);
                }
        }

        function loadRegistration(&$registration)
        {
                if (!is_object($registration))
                        return false;

                $keys = array('email', 'name', 'orgtype',
                                'countryresident', 'countryorigin',
                                'disability', 'hispanic', 'race',
                                'phone', 'reason', 'edulevel',
                                'role');

                foreach($keys as $key)
                        if ($registration->get($key) !== null)
                                $this->set($key, $registration->get($key));

                if ($registration->get('login') !== null)
					$this->set('username', $registration->get('login'));

                if ($registration->get('password') !== null)
                	$this->set('userPassword', $registration->get('password'));

                if ($registration->get('org') !== null)
					$this->set('organization', $registration->get('org'));

                if ($registration->get('sex') !== null)
					$this->set('gender', $registration->get('sex'));

                if ($registration->get('nativetribe') !== null)
					$this->set('nativeTribe', $registration->get('nativetribe'));

                if ($registration->get('web') !== null)
					$this->set('url', $registration->get('web'));

                if ($registration->get('mailPreferenceOption') !== null)
                        $this->set('mailPreferenceOption', $registration->get('mailPreferenceOption') ? '2' : '0');

                if ($registration->get('usageAgreement') !== null)
                        $this->set('usageAgreement', $registration->get('usageAgreement') ? true : false);

                return true;
        }

}
?>
