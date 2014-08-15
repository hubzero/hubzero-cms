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

namespace Hubzero\User;

use Hubzero\Base\Object;
use Hubzero\User\Profile\Helper as ProfileHelper;
use Hubzero\Utility\String;

/**
 * Extended user profile
 */
class Profile extends Object
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
	private $mailPreferenceOption = -1;
	
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
	
	// multi-value properties stored in auxilliary tables
	

	/**
	 * Description for '_auxs_bio'
	 *
	 * @var unknown
	 */
	private $_auxs_bio = null;
	
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

	/**
	 * Short description for 'setError'
	 * Long description (if any) ...
	 *
	 * @param string $msg Parameter description (if any) ...
	 * @return void
	 */
	public function setError($error, $key = null)
	{
		$bt = debug_backtrace();
		
		$error = __CLASS__ . "::" . $bt[1]['function'] . "():" . $error;
		
		array_push($this->_errors, $error);
	}

	/**
	 * Short description for 'clear'
	 * Long description (if any) ...
	 *
	 * @return boolean Return description (if any) ...
	 */
	public function clear()
	{
		$classvars = get_class_vars(__CLASS__);
		
		foreach ($classvars as $property=>$value)
		{
			if ('_s_' == substr($property, 0, 3)) // don't touch static variables
			{
				continue;
			}
			
			unset($this->$property);
			$this->$property = $value;
		}
		
		$objvars = get_object_vars($this);
		
		foreach ($objvars as $property=>$value)
		{
			if (!array_key_exists($property, $classvars))
			{
				unset($this->$property);
			}
		}
		
		return true;
	}

	/**
	 * Short description for '_mysql_load'
	 * Long description (if any) ...
	 *
	 * @param mixed $user Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	private function _mysql_load($user)
	{
		$db = \JFactory::getDBO();
		
		if (empty($user))
		{
			$this->setError('No user specified');
			return false;
		}
		
		// zooley: Removed check for >= 0 because profiles without acounts have negative IDs
		//if (is_numeric($user) && $user >= 0)
		if (is_numeric($user))
		{
			$query = "SELECT * FROM #__xprofiles WHERE uidNumber = " . $db->Quote(intval($user)) . ";";
		}
		else
		{
			$query = "SELECT * FROM #__xprofiles WHERE username = " . $db->Quote($user) . " AND uidNumber>0;";
		}
		
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
		
		$paramsClass = '\\JParameter';
		
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = '\\JRegistry';
		}
		
		$this->_params = new $paramsClass($result['params']);
		
		foreach ($result as $property=>$value)
		{
			$this->set($property, $value);
		}
		
		$classvars = get_class_vars(__CLASS__);
		
		foreach ($classvars as $property=>$value)
		{
			if ('_auxv_' == substr($property, 0, 6) || '_auxs_' == substr($property, 0, 6))
			{
				$this->$property = false; // this property is loaded on demand
			}
		}
		
		$this->_params->loadINI($this->params);
		
		return true;
	}

	/**
	 * Short description for '_mysql_author_load'
	 * Long description (if any) ...
	 *
	 * @param string $authorid Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	private function _mysql_author_load($authorid)
	{
		static $_propertyauthormap = array('uidNumber'=>'id', 'givenName'=>'firstname', 'middleName'=>'middlename', 'surname'=>'lastname', 'organization'=>'org', 'bio'=>'bio', 'url'=>'url', 'picture'=>'picture', 'vip'=>'principal_investigator');
		
		$db =  \JFactory::getDBO();
		
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
		
		foreach ($_propertyauthormap as $property=>$aproperty)
		{
			if (!empty($result[$aproperty]))
			{
				$this->set($property, $result[$aproperty]);
			}
		}
		
		return true;
	}

	/**
	 * Short description for '_xregistration_load'
	 * Long description (if any) ...
	 *
	 * @param object $registration Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	private function _xregistration_load($registration)
	{
		static $_propertyregmap = array('username'=>'login', 'name'=>'name', 'email'=>'email', 'orgtype'=>'orgtype', 'organization'=>'org', 'countryresident'=>'countryresident', 'countryorigin'=>'countryorigin', 'gender'=>'sex', 'url'=>'web', 'reason'=>'reason', 'mailPreferenceOption'=>'mailPreferenceOption', 'usageAgreement'=>'usageAgreement', 'nativeTribe'=>'nativeTribe', 'phone'=>'phone', 'disability'=>'disability', 'hispanic'=>'hispanic', 'race'=>'race', 'admin'=>'admin', 'host'=>'host', 'edulevel'=>'edulevel', 'role'=>'role', 'givenName'=>'givenName', 'middleName'=>'middleName', 'surname'=>'surname');
		
		if (!is_object($registration))
		{
			$this->setError("Invalid XRegistration object");
			return false;
		}
		
		foreach ($_propertyregmap as $property=>$rproperty)
		{
			if ($registration->get($rproperty) !== null)
			{
				$this->set($property, $registration->get($rproperty));
			}
		}
		
		$this->set('mailPreferenceOption', $this->get('mailPreferenceOption') ? $this->get('mailPreferenceOption') : '-1');
		$this->set('usageAgreement', $this->get('usageAgreement') ? '1' : '0');
		
		return true;
	}

	/**
	 * Short description for 'loadRegistration'
	 * Long description (if any) ...
	 *
	 * @param object &$registration Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function loadRegistration(&$registration)
	{
		if (!is_object($registration))
		{
			return false;
		}
		
		$keys = array('email', 'name', 'orgtype', 'countryresident', 'countryorigin', 'disability', 'hispanic', 'race', 'phone', 'reason', 'edulevel', 'role', 'surname', 'givenName', 'middleName');
		
		foreach ($keys as $key)
		{
			if ($registration->get($key) !== null)
			{
				$this->set($key, $registration->get($key));
			}
		}
		
		if ($registration->get('login') !== null)
		{
			$this->set('username', $registration->get('login'));
		}
		
		if ($registration->get('password') !== null)
		{
			$this->set('password', $registration->get('password'));
		}
		
		if ($registration->get('org') !== null || $registration->get('orgtext') !== null)
		{
			$this->set('organization', $registration->get('org'));
			
			if ($registration->get('orgtext'))
			{
				$this->set('organization', $registration->get('orgtext'));
			}
		}
		
		if ($registration->get('sex') !== null)
		{
			$this->set('gender', $registration->get('sex'));
		}
		
		if ($registration->get('nativetribe') !== null)
		{
			$this->set('nativeTribe', $registration->get('nativetribe'));
		}
		
		if ($registration->get('web') !== null)
		{
			$this->set('url', $registration->get('web'));
		}
		
		if ($registration->get('mailPreferenceOption') !== null)
		{
			$this->set('mailPreferenceOption', $registration->get('mailPreferenceOption') ? $registration->get('mailPreferenceOption') : '-1');
		}
		
		if ($registration->get('usageAgreement') !== null)
		{
			$this->set('usageAgreement', $registration->get('usageAgreement') ? true : false);
		}
		
		return true;
	}

	/**
	 * Short description for 'load'
	 * Long description (if any) ...
	 *
	 * @param unknown $user Parameter description (if any) ...
	 * @param string $storage Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function load($user, $storage = 'mysql')
	{
		if (!empty($storage) && !in_array($storage, array('mysql', 'author', 'xregistration')))
		{
			$this->setError('Invalid storage option requested [' . $storage . ']');
			return false;
		}
		
		if ($storage == 'mysql')
		{
			return $this->_mysql_load($user);
		}
		
		if ($storage == 'author')
		{
			return $this->_mysql_load_author($user);
		}
		
		if ($storage == 'xregistration')
		{
			return $this->_xregistration_load($user);
		}
		
		return true;
	}

	/**
	 * Short description for '__construct'
	 * Long description (if any) ...
	 *
	 * @param unknown $user Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function __construct($user = null)
	{
		if (!empty($user))
		{
			return $this->load($user);
		}
		
		return true;
	}

	/**
	 * Returns a reference to the global User object, only creating it if it doesn't already exist.
	 *
	 * @param mixed $user The user to load - Can be an integer or string
	 * @return mixed Returns object if valid record found, false if not
	 */
	public static function getInstance($id = null)
	{
		static $instances;
		static $usernames;
		
		if (!isset($instances))
		{
			$instances = array();
		}
		
		if (!isset($usernames))
		{
			$usernames = array();
		}
		
		// Is this a username?
		if (!is_numeric($id))
		{
			// Normalize and check if we have data for this username
			$id = strtolower(trim($id));
			if (!isset($usernames[$id]))
			{
				$user = new self($id);
				
				// Save
				$usernames[$id] = $user->get('uidNumber');
				$instances[$usernames[$id]] = $user;
			}
			// Change the $id from username to numeric ID
			$id = $usernames[$id];
		}
		
		// Check for existing record
		if (empty($instances[$id]) || $instances[$id]->get('uidNumber') != $id)
		{
			$user = new self($id);
			$instances[$id] = $user;
		}
		
		// Ensure record has data
		if (!$instances[$id]->get('uidNumber'))
		{
			return false;
		}
		
		return $instances[$id];
	}

	/**
	 * Short description for 'create'
	 * Long description (if any) ...
	 *
	 * @return boolean Return description (if any) ...
	 */
	public function create()
	{
		$db =  \JFactory::getDBO();
		
		$modifiedDate = gmdate('Y-m-d H:i:s');
		
		if (is_numeric($this->get('uidNumber')))
		{
			$query = "INSERT INTO #__xprofiles (uidNumber,username,modifiedDate) VALUE (" . $db->Quote($this->get('uidNumber')) . ',' . $db->Quote($this->get('username')) . ',' . $db->Quote($modifiedDate) . ");";
			
			$db->setQuery($query);
			
			if (!$db->query())
			{
				$errno = $db->getErrorNum();
				
				if ($errno == 1062)
				{
					$this->setError('uidNumber (' . $this->get('uidNumber') . ') already exists' . ' in xprofiles table');
				}
				else
				{
					$this->setError('Error inserting user data to xprofiles table: ' . $db->getErrorMsg());
				}
				
				return false;
			}
		}
		else
		{
			$token = uniqid();
			
			$query = "INSERT INTO #__xprofiles (uidNumber,username,modifiedDate) SELECT " . "IF(MIN(uidNumber)>0,-1,MIN(uidNumber)-1)," . $db->Quote($token) . ',' . $db->Quote($modifiedDate) . " FROM #__xprofiles;";
			
			$db->setQuery($query);
			
			if (!$db->query())
			{
				$this->setError('Error inserting non-user data to xprofiles table: ' . $db->getErrorMsg());
				
				return false;
			}
			
			$query = "SELECT uidNumber from #__xprofiles WHERE username=" . $db->Quote($token) . " AND modifiedDate=" . $db->Quote($modifiedDate);
			
			$db->setQuery($query);
			
			$result = $db->loadResultArray();
			
			if ($result === false)
			{
				$this->setError('Error adding data to xprofiles table: ' . $db->getErrorMsg());
				
				return false;
			}
			
			if (count($result) > 1)
			{
				$this->setError('Error adding data to xprofiles table: ' . $db->getErrorMsg());
				
				return false;
			}
			
			$this->set('uidNumber', $result[0]);
		}
		
		$this->set('modifiedDate', $modifiedDate);
		
		if ($this->update() === false)
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Short description for 'read'
	 * Long description (if any) ...
	 *
	 * @param boolean $mysqlonly Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function read($instance = null)
	{
		return $this->load($instance);
	}

	/**
	 * Short description for 'update'
	 * Long description (if any) ...
	 *
	 * @param boolean $mysqlonly Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function store()
	{
		if (!is_numeric($this->get('uidNumber')))
		{
			return $this->create();
		}
		return $this->update();
	}

	/**
	 * Short description for 'update'
	 * Long description (if any) ...
	 *
	 * @param boolean $mysqlonly Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function update()
	{
		if (!is_numeric($this->get('uidNumber')))
		{
			return false;
		}
		
		$db =  \JFactory::getDBO();
		
		$modifiedDate = gmdate('Y-m-d H:i:s');
		
		$this->set('modifiedDate', $modifiedDate);
		
		$query = "UPDATE #__xprofiles SET ";
		
		$classvars = get_class_vars(__CLASS__);
		
		$first = true;
		$affected = 0;
		
		foreach ($classvars as $property=>$value)
		{
			if ('_' == substr($property, 0, 1))
			{
				continue;
			}
			
			if (!$first)
			{
				$query .= ',';
			}
			else
			{
				$first = false;
			}
			
			if($property == 'params')
			{
				if (is_object($this->_params))
				{
					$query .= "params='".str_replace("", "",$this->_params->toString())."'";
				}
				else
				{
					$query .= "params=''";
				}
				continue;
			}
			
			if ($this->get($property) === null)
			{
				$query .= "$property=NULL";
			}
			else
			{
				$query .= "$property=" . $db->Quote($this->get($property));
			}
		}
		
		$query .= " WHERE uidNumber=" . $db->Quote($this->get('uidNumber')) . ";";
		
		$db->setQuery($query);
		
		if (!$db->query())
		{
			$this->setError('Error updating data in xprofiles table: ' . $db->getErrorMsg());
		}
		
		$affected = $db->getAffectedRows();
		
		foreach ($classvars as $property=>$value)
		{
			if (('_auxv_' != substr($property, 0, 6)) && ('_auxs_' != substr($property, 0, 6)))
			{
				continue;
			}
			
			$property = substr($property, 6);
			
			$first = true;
			
			$query = "REPLACE INTO #__xprofiles_" . $property . " (uidNumber, " . $property . ") VALUES ";
			$query_values = "";
			
			$list = $this->get($property);
			
			if (!is_array($list))
			{
				$list = array($list);
			}
			
			foreach ($list as $value)
			{
				if (!$first)
				{
					$query_values .= ',';
				}
				
				$first = false;
				
				$query_values .= '(' . $db->Quote($this->get('uidNumber')) . ',' . $db->Quote($value) . ')';
			}
			
			if ($query_values != '')
			{
				$db->setQuery($query . $query_values);
				
				if (!$db->query())
				{
					$this->setError("Error updating data in xprofiles $property table: " . $db->getErrorMsg());
				}
				else
				{
					$affected += $db->getAffectedRows();
				}
			}
			
			if (property_exists(__CLASS__, '_auxv_' . $property))
			{
				foreach ($list as $key=>$value)
				{
					$list[$key] = $db->Quote($value);
				}
				
				$valuelist = implode($list, ",");
				
				if (empty($valuelist))
				{
					$valuelist = "''";
				}
				
				$query = "DELETE FROM #__xprofiles_" . $property . " WHERE uidNumber=" . $this->get('uidNumber') . " AND $property NOT IN ($valuelist);";
				
				$db->setQuery($query);
				
				if (!$db->query())
				{
					$this->setError("Error deleting data in xprofiles $property table: " . $db->getErrorMsg());
				}
				else
				{
					$affected += $db->getAffectedRows();
				}
			}
		}
		
		if ($affected > 0)
		{
			\JPluginHelper::importPlugin('user');
			\JDispatcher::getInstance()->trigger('onAfterStoreProfile', array($this));
		}
		
		return true;
	}

	/**
	 * Short description for 'delete'
	 * Long description (if any) ...
	 *
	 * @return boolean Return description (if any) ...
	 */
	public function delete()
	{
		$db =  \JFactory::getDBO();
		
		if (!is_numeric($this->get('uidNumber')))
		{
			$this->setError("missing required field 'uidNumber'");
			return false;
		}
		
		$classvars = get_class_vars(__CLASS__);
		
		$affected = 0;
		
		foreach ($classvars as $property=>$value)
		{
			if ('_auxv_' != substr($property, 0, 6) && '_auxs_' != substr($property, 0, 6))
			{
				continue;
			}
			
			$property = substr($property, 6);
			
			$query = "DELETE FROM #__xprofiles_$property WHERE uidNumber = '" . $this->get('uidNumber') . "'";
			$db->setQuery($query);
			
			if (!$db->query())
			{
				$this->setError("Error deleting from xprofiles $property table: " . $db->getErrorMsg());
			}
			else
			{
				$affected += $db->getAffectedRows();
			}
		}
		
		$query = "DELETE FROM #__xprofiles WHERE uidNumber = '" . $this->get('uidNumber') . "'";
		$db->setQuery($query);
		
		if (!$db->query())
		{
			$this->setError("Error deleting from xprofiles table: " . $db->getErrorMsg());
		}
		else
		{
			$affected += $db->getAffectedRows();
		}
		
		if ($affected > 0)
		{
			\JPluginHelper::importPlugin('user');
			\JDispatcher::getInstance()->trigger('onAfterDeleteProfile', array($this));
		}
		
		$this->clear();
		
		return true;
	}

	/**
	 * Get a property's value
	 *
	 * @param  string $property Name of the property to retrieve
	 * @param  mixed  $value    Default value
	 * @return mixed
	 */
	public function get($property, $default = null)
	{
		if ($property == 'password')
		{
			return $this->_password;
		}

		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (!property_exists(__CLASS__, $property))
		{
			if (property_exists(__CLASS__, '_auxs_' . $property))
			{
				$property = '_auxs_' . $property;
			}
			else if (property_exists(__CLASS__, '_auxv_' . $property))
			{
				$property = '_auxv_' . $property;
			}
			else
			{
				$this->setError("Unknown property: $property");
				return false;
			}
		}

		if ($this->$property === false)
		{
			$db = \JFactory::getDBO();

			$property_name = substr($property, 6);

			$query = "SELECT $property_name FROM #__xprofiles AS x,#__xprofiles_$property_name AS xp WHERE " . "x.uidNumber=xp.uidNumber AND xp.uidNumber=" . $db->Quote($this->get('uidNumber')) . " ORDER BY $property_name ASC;";

			$db->setQuery($query);

			$result = $db->loadResultArray();

			if ($result === false)
			{
				$this->setError("Error retrieving data from xprofiles $property table: " . $db->getErrorMsg());
			}
			else if ('_auxs_' == substr($property, 0, 6))
			{
				if (isset($result[0]))
				{
					$this->set($property_name, $result[0]);
				}
				else
				{
					$this->set($property_name, '');
				}
			}
			else
			{
				if (is_array($result) && count($result) <= 1 && empty($result[0]))
				{
					$this->set($property_name, array());
				}
				else
				{
					$this->set($property_name, $result);
				}
			}
		}

		return $this->$property;
	}

	/**
	 * Short description for 'set'
	 * Long description (if any) ...
	 *
	 * @param string $property Parameter description (if any) ...
	 * @param mixed $value Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function set($property, $value = null)
	{
		if ($property == 'password')
		{
			if ($value != '')
			{
				$this->userPassword = "{MD5}" . base64_encode(pack('H*', md5($value)));
			}
			else
			{
				$this->userPassword = '';
			}
			
			$this->_password = $value;
			
			return true;
		}
		
		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}
		
		if (!property_exists(__CLASS__, $property))
		{
			if (property_exists(__CLASS__, '_auxs_' . $property))
			{
				$property = '_auxs_' . $property;
			}
			else if (property_exists(__CLASS__, '_auxv_' . $property))
			{
				$property = '_auxv_' . $property;
			}
			else
			{
				$this->setError("Unknown property: $property");
				return false;
			}
		}
		
		if ('_auxv_' == substr($property, 0, 6))
		{
			if (empty($value))
			{
				$value = array();
			}
			else
			{
				if (!is_array($value))
				{
					$value = array($value);
				}
				
				$list = array_unique($value);
				sort($list);
				unset($value);
				
				foreach ($list as $v)
				{
					$value[] = strval($v);
				}
			}
		}
		else
			$value = strval($value);
		
		$this->$property = $value;
		
		if ($property == 'userPassword')
		{
			$this->_password = '';
		}
		
		return true;
	}

	/**
	 * Short description for 'add'
	 * Long description (if any) ...
	 *
	 * @param string $property Parameter description (if any) ...
	 * @param array $value Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function add($property, $value)
	{
		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}
		
		if (property_exists(__CLASS__, $property) || property_exists(__CLASS__, '_auxs_' . $property))
		{
			$this->setError("Can't add value(s) to non-array property.");
			return false;
		}
		
		if (!property_exists(__CLASS__, '_auxv_' . $property))
		{
			$this->setError("Unknown property: $property");
			return false;
		}
		
		if (empty($value))
		{
			return true;
		}
		
		if (!is_array($value))
		{
			$value = array($value);
		}
		
		$property = '_auxv_' . $property;
		
		foreach ($value as $v)
		{
			$v = strval($v);
			
			if (!in_array($v, $this->$property))
			{
				array_push($this->$property, $v);
			}
		}
		
		sort($this->$property);
		
		return true;
	}

	/**
	 * Short description for 'remove'
	 * Long description (if any) ...
	 *
	 * @param string $property Parameter description (if any) ...
	 * @param array $value Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function remove($property, $value)
	{
		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}
		
		if (property_exists(__CLASS__, $property) || property_exists(__CLASS__, '_auxs_' . $property))
		{
			$this->setError("Can't remove value(s) from non-array property.");
			return false;
		}
		
		if (!property_exists(__CLASS__, '_auxv_' . $property))
		{
			$this->setError("Unknown property: $property");
			return false;
		}
		
		if (!isset($value))
		{
			return true;
		}
		
		if (!is_array($value))
		{
			$value = array($value);
		}
		
		$property = '_auxv_' . $property;
		
		foreach ($value as $v)
		{
			$v = strval($v);
			
			if (in_array($v, $this->$property))
			{
				$this->$property = array_diff($this->$property, array($v));
			}
		}
		
		return true;
	}

	/**
	 * Short description for 'getParam'
	 * Long description (if any) ...
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @param unknown $default Parameter description (if any) ...
	 * @return object Return description (if any) ...
	 */
	public function getParam($key, $default = null)
	{
		return $this->_params->get($key, $default);
	}

	/**
	 * Short description for 'setParam'
	 * Long description (if any) ...
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @param unknown $value Parameter description (if any) ...
	 * @return object Return description (if any) ...
	 */
	public function setParam($key, $value)
	{
		return $this->_params->set($key, $value);
	}

	/**
	 * Short description for 'defParam'
	 * Long description (if any) ...
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @param unknown $value Parameter description (if any) ...
	 * @return object Return description (if any) ...
	 */
	public function defParam($key, $value)
	{
		return $this->_params->def($key, $value);
	}

	/**
	 * Short description for 'getParameters'
	 * Long description (if any) ...
	 *
	 * @param boolean $loadsetupfile Parameter description (if any) ...
	 * @param unknown $path Parameter description (if any) ...
	 * @return object Return description (if any) ...
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
	 * Long description (if any) ...
	 *
	 * @param unknown $params Parameter description (if any) ...
	 * @return void
	 */
	public function setParameters($params)
	{
		$this->_params = $params;
	}
	
	/* Member Roles */
	
	/**
	 * Short description for 'getGroupMemberRoles'
	 * Long description (if any) ...
	 *
	 * @param string $uid Parameter description (if any) ...
	 * @param string $gid Parameter description (if any) ...
	 * @return unknown Return description (if any) ...
	 */
	public static function getGroupMemberRoles($uid, $gid)
	{
		$db = \JFactory::getDBO();
		$sql = "SELECT r.id, r.name, r.permissions FROM #__xgroups_roles as r, #__xgroups_member_roles as m WHERE r.id=m.roleid AND m.uidNumber='" . $uid . "' AND r.gidNumber='" . $gid . "'";
		$db->setQuery($sql);
		
		return $db->loadAssocList();
	}
	
	/**
	 * Check to see if user has permission to perform task
	 *
	 * @param     $group     \Hubzero\User\Group Object
	 * @param     $action    Group Action to perform
	 * @return    bool
	 */
	public static function userHasPermissionForGroupAction( $group, $action )
	{
		//get user roles
		$roles = self::getGroupMemberRoles( 
			\JFactory::getUser()->get('id'), 
			$group->get('gidNumber')
		);

		// check to see if any of our roles for user has permission for action
		foreach ($roles as $role)
		{
			$permissions = json_decode($role['permissions']);
			$permissions = (is_object($permissions)) ? $permissions : new stdClass;
			if (property_exists($permissions, $action) && $permissions->$action == 1)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Short description for 'getCourseMemberRoles'
	 * Long description (if any) ...
	 *
	 * @param string $uid Parameter description (if any) ...
	 * @param string $gid Parameter description (if any) ...
	 * @return unknown Return description (if any) ...
	 */
	public function getCourseMemberRoles($uid, $gid)
	{
		$user_roles = '';
		
		$db =  \JFactory::getDBO();
		$sql = "SELECT r.id, r.role FROM #__courses_roles as r, #__courses_member_roles as m WHERE r.id=m.role AND m.uidNumber='" . $uid . "' AND r.gidNumber='" . $gid . "'";
		$db->setQuery($sql);
		
		$roles = $db->loadAssocList();
		
		if ($roles)
		{
			return $roles;
		}
	}

	/**
	 * Get the groups for a user
	 *
	 * @param string $role The group set to return. Returns all groups if not set
	 * @return array Array of groups
	 */
	public function getGroups($role = 'all')
	{
		static $groups;
		
		if (!isset($groups))
		{
			$groups = array('applicants'=>array(), 'invitees'=>array(), 'members'=>array(), 'managers'=>array(), 'all'=>array());
			$groups['all'] = Helper::getGroups($this->get('uidNumber'), 'all', 1);
			
			if ($groups['all'])
			{
				foreach ($groups['all'] as $item)
				{
					if ($item->registered)
					{
						if (!$item->regconfirmed)
						{
							$groups['applicants'][] = $item;
						}
						else
						{
							if ($item->manager)
							{
								$groups['managers'][] = $item;
							}
							else
							{
								$groups['members'][] = $item;
							}
						}
					}
					else
					{
						$groups['invitees'][] = $item;
					}
				}
			}
		}
		
		if ($role)
		{
			return (isset($groups[$role])) ? $groups[$role] : false;
		}
		
		return $groups;
	}

	/**
	 * Get the content of the entry
	 * 
	 * @param      string  $as      Format to return state in [text, number]
	 * @param      integer $shorten Number of characters to shorten text to
	 * @return     string
	 */
	public function getBio($as='parsed', $shorten=0)
	{
		$options = array();

		switch (strtolower($as))
		{
			case 'parsed':
				$config = array(
					'option'   => 'com_members',
					'scope'    => 'profile',
					'pagename' => 'member',
					'pageid'   => 0,
					'filepath' => '',
					'domain'   => '',
					'camelcase' => 0
				);

				\JPluginHelper::importPlugin('content');
				\JDispatcher::getInstance()->trigger('onContentPrepare', array(
					'com_members.profile.bio',
					&$this,
					&$config
				));
				$content = $this->get('bio');

				$options = array('html' => true);
			break;

			case 'clean':
				$content = strip_tags($this->getBio('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('bio'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = String::truncate($content, $shorten, $options);
		}

		return $content;
	}

	/**
	 * Get a user's picture
	 *
	 * @param    integer $anonymous Is user anonymous?
	 * @param    boolean $thumbit   Show thumbnail or full picture?
	 * @return   string
	 */
	public function getPicture($anonymous=0, $thumbit=true)
	{
		return ProfileHelper::getMemberPhoto($this, $anonymous, $thumbit);
	}
}
