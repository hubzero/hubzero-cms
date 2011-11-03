<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_User_Profile_Helper');

/**
 * Short description for 'Hubzero_User_Profile'
 * 
 * Long description (if any) ...
 */
class Hubzero_User_Profile extends JObject
{
	// properties

	/**
	 * Description for 'uidNumber'
	 * 
	 * @var unknown
	 */
	private $uidNumber = null;

	/**
	 * Description for 'name'
	 * 
	 * @var unknown
	 */
	private $name = null;

	/**
	 * Description for 'username'
	 * 
	 * @var unknown
	 */
	private $username = null;

	/**
	 * Description for 'email'
	 * 
	 * @var unknown
	 */
	private $email = null;

	/**
	 * Description for 'registerDate'
	 * 
	 * @var unknown
	 */
	private $registerDate = null;

	/**
	 * Description for 'gidNumber'
	 * 
	 * @var unknown
	 */
	private $gidNumber = null;

	/**
	 * Description for 'homeDirectory'
	 * 
	 * @var unknown
	 */
	private $homeDirectory = null;

	/**
	 * Description for 'loginShell'
	 * 
	 * @var unknown
	 */
	private $loginShell = null;

	/**
	 * Description for 'ftpShell'
	 * 
	 * @var unknown
	 */
	private $ftpShell = null;

	/**
	 * Description for 'userPassword'
	 * 
	 * @var string
	 */
	private $userPassword = null;

	/**
	 * Description for 'shadowExpire'
	 * 
	 * @var unknown
	 */
	private $shadowExpire = null;

	/**
	 * Description for 'gid'
	 * 
	 * @var unknown
	 */
	private $gid = null;

	/**
	 * Description for 'orgtype'
	 * 
	 * @var unknown
	 */
	private $orgtype = null;

	/**
	 * Description for 'organization'
	 * 
	 * @var unknown
	 */
	private $organization = null;

	/**
	 * Description for 'countryresident'
	 * 
	 * @var unknown
	 */
	private $countryresident = null;

	/**
	 * Description for 'countryorigin'
	 * 
	 * @var unknown
	 */
	private $countryorigin = null;

	/**
	 * Description for 'gender'
	 * 
	 * @var unknown
	 */
	private $gender = null;

	/**
	 * Description for 'url'
	 * 
	 * @var unknown
	 */
	private $url = null;

	/**
	 * Description for 'reason'
	 * 
	 * @var unknown
	 */
	private $reason = null;

	/**
	 * Description for 'mailPreferenceOption'
	 * 
	 * @var unknown
	 */
	private $mailPreferenceOption = null;

	/**
	 * Description for 'usageAgreement'
	 * 
	 * @var unknown
	 */
	private $usageAgreement = null;

	/**
	 * Description for 'jobsAllowed'
	 * 
	 * @var unknown
	 */
	private $jobsAllowed = null;

	/**
	 * Description for 'modifiedDate'
	 * 
	 * @var unknown
	 */
	private $modifiedDate = null;

	/**
	 * Description for 'emailConfirmed'
	 * 
	 * @var unknown
	 */
	private $emailConfirmed = null;

	/**
	 * Description for 'regIP'
	 * 
	 * @var unknown
	 */
	private $regIP = null;

	/**
	 * Description for 'regHost'
	 * 
	 * @var unknown
	 */
	private $regHost = null;

	/**
	 * Description for 'nativeTribe'
	 * 
	 * @var unknown
	 */
	private $nativeTribe = null;

	/**
	 * Description for 'phone'
	 * 
	 * @var unknown
	 */
	private $phone = null;

	/**
	 * Description for 'proxyPassword'
	 * 
	 * @var unknown
	 */
	private $proxyPassword = null;

	/**
	 * Description for 'proxyUidNumber'
	 * 
	 * @var unknown
	 */
	private $proxyUidNumber = null;

	/**
	 * Description for 'givenName'
	 * 
	 * @var unknown
	 */
	private $givenName = null;

	/**
	 * Description for 'middleName'
	 * 
	 * @var unknown
	 */
	private $middleName = null;

	/**
	 * Description for 'surname'
	 * 
	 * @var unknown
	 */
	private $surname = null;

	/**
	 * Description for 'picture'
	 * 
	 * @var unknown
	 */
	private $picture = null;

	/**
	 * Description for 'vip'
	 * 
	 * @var unknown
	 */
	private $vip = null;

	/**
	 * Description for 'public'
	 * 
	 * @var unknown
	 */
	private $public = null;

	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	private $params = null;

	/**
	 * Description for 'note'
	 * 
	 * @var unknown
	 */
	private $note = null;
	// properties stored in auxilliary tables

	/**
	 * Description for '_auxs_bio'
	 * 
	 * @var unknown
	 */
	private $_auxs_bio = null;
	// multi-value properties stored in auxilliary tables

	/**
	 * Description for '_auxv_disability'
	 * 
	 * @var array
	 */
	private $_auxv_disability = array();

	/**
	 * Description for '_auxv_hispanic'
	 * 
	 * @var array
	 */
	private $_auxv_hispanic = array();

	/**
	 * Description for '_auxv_race'
	 * 
	 * @var array
	 */
	private $_auxv_race = array();

	/**
	 * Description for '_auxv_admin'
	 * 
	 * @var array
	 */
	private $_auxv_admin = array();

	/**
	 * Description for '_auxv_host'
	 * 
	 * @var array
	 */
	private $_auxv_host = array();

	/**
	 * Description for '_auxv_manager'
	 * 
	 * @var array
	 */
	private $_auxv_manager = array();

	/**
	 * Description for '_auxv_edulevel'
	 * 
	 * @var array
	 */
	private $_auxv_edulevel = array();

	/**
	 * Description for '_auxv_role'
	 * 
	 * @var array
	 */
	private $_auxv_role = array();
	//private $_auxv_tag = array();
	// private class variables

	/**
	 * Description for '_password'
	 * 
	 * @var string
	 */
	private $_password = null;

	/**
	 * Description for '_params'
	 * 
	 * @var object
	 */
	private $_params = null;

	// static class variables

	/**
	 * Description for '_s_propertyattrmap'
	 * 
	 * @var array
	 */
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

	/**
	 * Short description for 'setError'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @return     void
	 */
	public function setError($msg)
	{
		$bt = debug_backtrace();

		$error = "Hubzero_User_Profile::" . $bt[1]['function'] . "():" . $msg;

		array_push($this->_errors, $error);
	}

	/**
	 * Short description for 'logDebug'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $msg Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function logDebug($msg)
	{
		$xlog =& Hubzero_Factory::getLogger();
		$xlog->logDebug($msg);

		return true;
	}

	/**
	 * Short description for 'clear'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function clear()
	{
		$classvars = get_class_vars('Hubzero_User_Profile');

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

	/**
	 * Short description for '_ldap_get_user'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $username Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _ldap_get_user($username = null)
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();

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

		if (is_numeric($username) && $username >= 0)
		{
			$dn = 'ou=users,' . $hubLDAPBaseDN;
			$filter = '(uidNumber=' . $username . ')';
		}
		else
		{
			$dn = "uid=$username,ou=users," . $hubLDAPBaseDN;
			$filter = '(objectclass=*)';
		}

		$attributes[] = 'sn';
		$attributes[] = 'member';

		foreach(Hubzero_User_Profile::$_s_propertyattrmap as $property => $attribute)
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

		foreach(Hubzero_User_Profile::$_s_propertyattrmap as $property => $attribute)
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

					if (property_exists('Hubzero_User_Profile','_auxv_' . $property))
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

	/**
	 * Short description for '_ldap_load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $username Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _ldap_load($username = null)
	{
		$userinfo = $this->_ldap_get_user($username);

		if ($userinfo == false)
			return false;

		$this->clear();

		foreach(Hubzero_User_Profile::$_s_propertyattrmap as $property => $attribute)
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

	/**
	 * Short description for '_mysql_load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $user Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _mysql_load($user)
	{
		$db = &JFactory::getDBO();

		if (empty($user))
		{
			$this->setError('No user specified');
			return false;
		}

		if (is_numeric($user) && $user >= 0)
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

		$classvars = get_class_vars('Hubzero_User_Profile');

		foreach ($classvars as $property => $value)
		{
			if ('_auxv_' == substr($property, 0, 6) || '_auxs_' == substr($property, 0, 6))
				$this->$property = false; // this property is loaded on demand
		}

		$this->_params->loadINI($this->params);

		return true;
	}

	/**
	 * Short description for '_mysql_author_load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $authorid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _mysql_author_load($authorid)
	{
		static $_propertyauthormap = array('uidNumber' => 'id', 'givenName' => 'firstname', 'middleName' => 'middlename',
			'surname' => 'lastname', 'organization' => 'org', 'bio' => 'bio', 'url' => 'url', 'picture' => 'picture',
			'vip' => 'principal_investigator');

		$db = & JFactory::getDBO();
		$xhub = &Hubzero_Factory::getHub();

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

	/**
	 * Short description for '_xregistration_load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $registration Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $user Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $user Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function __construct($user = null)
	{
		if (!empty($user))
			return $this->load($user);

		return true;
	}

	/**
	 * Short description for '_ldap_create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
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

		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();

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

		foreach(Hubzero_User_Profile::$_s_propertyattrmap as $property => $attribute)
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

	/**
	 * Short description for '_mysql_create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	private function _mysql_create()
	{
		$db = &JFactory::getDBO();
		$xhub = &Hubzero_Factory::getHub();

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

	/**
	 * Short description for 'create'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for '_ldap_update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	private function _ldap_update()
	{
		$xhub = &Hubzero_Factory::getHub();
		$conn = &Hubzero_Factory::getPLDC();
		$errno = 0;

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (empty($hubLDAPBaseDN))
		{
			$this->setError("hubLDAPBaseDN variable not configured");
			return false;
		}

		$userinfo = $this->_ldap_get_user( $this->get('uidNumber') );

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
			$olddn = "uid=" . $userinfo['uid'] . ',ou=users,' . $hubLDAPBaseDN;
			$rdn = 'uid=' . $this->get('username');
			ldap_rename($conn, $olddn, $rdn, 'ou=users,' . $hubLDAPBaseDN, true);
			$userinfo['uid'] = $this->get('username');
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

		foreach(Hubzero_User_Profile::$_s_propertyattrmap as $property => $attribute)
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

	/**
	 * Short description for '_mysql_update_auxilliary_tables'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	private function _mysql_update_auxilliary_tables()
	{
		$db = &JFactory::getDBO();

		$classvars = get_class_vars('Hubzero_User_Profile');

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

			if (property_exists('Hubzero_User_Profile', '_auxv_'.$property))
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

	/**
	 * Short description for '_mysql_update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $mysqlonly Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _mysql_update($mysqlonly = false)
	{
		if (!is_numeric($this->get('uidNumber')))
			return false;

		$db = &JFactory::getDBO();

		$query = "UPDATE #__xprofiles SET ";

		$classvars = get_class_vars('Hubzero_User_Profile');
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

	/**
	 * Short description for 'update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'store'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $updateOnly Parameter description (if any) ...
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $user Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	static function getInstance($user = null)
	{
		$instance = new Hubzero_User_Profile($user);

		if ($instance->get('uidNumber') == '')
			return false;

		return $instance;
	}

	/**
	 * Short description for '_get_auxilliary_property'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $property Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $property Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function get($property)
	{
		if ($property == 'password')
			return $this->_password;

		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (!property_exists('Hubzero_User_Profile',$property))
		{
			if (property_exists('Hubzero_User_Profile','_auxs_' . $property))
				$property = '_auxs_' . $property;
			else if (property_exists('Hubzero_User_Profile','_auxv_' . $property))
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

	/**
	 * Short description for 'set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $property Parameter description (if any) ...
	 * @param      mixed $value Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

		if (!property_exists('Hubzero_User_Profile', $property))
		{
			if (property_exists('Hubzero_User_Profile','_auxs_' . $property))
				$property = '_auxs_' . $property;
			else if (property_exists('Hubzero_User_Profile','_auxv_' . $property))
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

	/**
	 * Short description for 'add'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $property Parameter description (if any) ...
	 * @param      array $value Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function add($property, $value)
	{
		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (property_exists('Hubzero_User_Profile',$property) || property_exists('Hubzero_User_Profile','_auxs_' . $property))
		{
			$this->setError("Can't add value(s) to non-array property.");
			return false;
		}

		if (!property_exists('Hubzero_User_Profile','_auxv_' . $property))
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

	/**
	 * Short description for 'remove'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $property Parameter description (if any) ...
	 * @param      array $value Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function remove($property, $value)
	{
		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (property_exists('Hubzero_User_Profile',$property) || property_exists('Hubzero_User_Profile','_auxs_' . $property))
		{
			$this->setError("Can't remove value(s) from non-array property.");
			return false;
		}

		if (!property_exists('Hubzero_User_Profile', '_auxv_' . $property))
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

	/**
	 * Short description for 'getParam'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $default Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getParam( $key, $default = null )
	{
		return $this->_params->get( $key, $default );
	}

	/**
	 * Short description for 'setParam'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function setParam( $key, $value )
	{
		return $this->_params->set( $key, $value );
	}

	/**
	 * Short description for 'defParam'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function defParam( $key, $value )
	{
		return $this->_params->def( $key, $value );
	}

	/**
	 * Short description for 'getParameters'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $loadsetupfile Parameter description (if any) ...
	 * @param      unknown $path Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function &getParameters($loadsetupfile = false, $path = null)
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

	/**
	 * Short description for 'setParameters'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @return     void
	 */
	public function setParameters($params)
	{
		$this->_params = $params;
	}

	/**
	 * Short description for '_ldap_delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	private function _ldap_delete($uid)
	{
		$xhub =& Hubzero_Factory::getHub();
		$conn =& Hubzero_Factory::getPLDC();

		$hubLDAPBaseDN = $xhub->getCfg('hubLDAPBaseDN');

		if (empty($hubLDAPBaseDN))
		{
			$this->setError("hubLDAPBaseDN variable not configured");
			return false;
		}

		if (empty($uid))
		{
			$this->setError("missing parameter 'uid'");
			return false;
		}

		$dn = "uid=" . $uid . ",ou=users," . $hubLDAPBaseDN;

		if (!@ldap_delete($conn, $dn))
		{
			$this->setError("ldap_delete() failed: " . ldap_error($conn));
			return false;
		}

		return true;
	}

	/**
	 * Short description for '_mysql_delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	private function _mysql_delete()
	{
		$db = & JFactory::getDBO();

		if (!is_numeric($this->get('uidNumber')))
		{
			$this->setError("missing required field 'uidNumber'");
			return false;
		}

		$classvars = get_class_vars('Hubzero_User_Profile');

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

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $storage Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function delete($storage = null)
	{
		if (!empty($storage) && !in_array($storage,array('mysql','ldap')))
		{
			$this->setError('Invalid storage option requested [' . $storage . ']');
			return false;
		}

		$mconfig = & JComponentHelper::getParams( 'com_members' );
		$ldapProfileMirror = $mconfig->get('ldapProfileMirror');

		$uid = $this->username;

		if (empty($storage))
			$storage = ($ldapProfileMirror) ? 'all' : 'mysql';

		if ($storage == 'mysql' || $storage == 'all')
			if ($this->_mysql_delete() === false)
				return false;

		if ($storage == 'ldap' || $storage == 'all')
			if ($this->_ldap_delete($uid) === false)
				return false;

		return true;
	}

	/**
	 * Short description for 'loadRegistration'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$registration Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadRegistration(&$registration)
	{
		if (!is_object($registration)) {
			return false;
		}

        $keys = array('email', 'name', 'orgtype',
                        'countryresident', 'countryorigin',
                        'disability', 'hispanic', 'race',
                        'phone', 'reason', 'edulevel',
                        'role');

		foreach ($keys as $key)
		{
			if ($registration->get($key) !== null) {
				$this->set($key, $registration->get($key));
			}
		}

		if ($registration->get('login') !== null) {
			$this->set('username', $registration->get('login'));
		}

		if ($registration->get('password') !== null) {
			$this->set('password', $registration->get('password'));
		}

		if ($registration->get('org') !== null) {
			$this->set('organization', $registration->get('org'));
		}

		if ($registration->get('sex') !== null) {
			$this->set('gender', $registration->get('sex'));
		}

		if ($registration->get('nativetribe') !== null) {
			$this->set('nativeTribe', $registration->get('nativetribe'));
		}

		if ($registration->get('web') !== null) {
			$this->set('url', $registration->get('web'));
		}

		if ($registration->get('mailPreferenceOption') !== null) {
			$this->set('mailPreferenceOption', $registration->get('mailPreferenceOption') ? '2' : '0');
		}

		if ($registration->get('usageAgreement') !== null) {
			$this->set('usageAgreement', $registration->get('usageAgreement') ? true : false);
		}

		return true;
	}

	/* Member Roles */

	/**
	 * Short description for 'getGroupMemberRoles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      string $gid Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function getGroupMemberRoles( $uid, $gid )
	{
		$user_roles = '';

		$db = & JFactory::getDBO();
		$sql = "SELECT r.id, r.role FROM #__xgroups_roles as r, #__xgroups_member_roles as m WHERE r.id=m.role AND m.uidNumber='".$uid."' AND r.gidNumber='".$gid."'";
		$db->setQuery($sql);

		$roles = $db->loadAssocList();

		if($roles) {
			return $roles;
		}
	}

}

