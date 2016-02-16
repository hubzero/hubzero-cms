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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

use Hubzero\Base\Object;
use Hubzero\User\Profile\Helper as ProfileHelper;
use Hubzero\User\Password;
use Hubzero\Config\Registry;
use Hubzero\Utility\String;
use Event;

/**
 * Extended user profile
 */
class Profile extends Object
{
	/**
	 * ID (primary key)
	 *
	 * @var  integer
	 */
	private $uidNumber = null;

	/**
	 * Name
	 *
	 * @var  string
	 */
	private $name = null;

	/**
	 * Username
	 *
	 * @var  string
	 */
	private $username = null;

	/**
	 * Email address
	 *
	 * @var  string
	 */
	private $email = null;

	/**
	 * Timestamp for registration date
	 *
	 * @var  string
	 */
	private $registerDate = null;

	/**
	 * Description for 'gidNumber'
	 *
	 * @var  integer
	 */
	private $gidNumber = null;

	/**
	 * System home directory for the user
	 *
	 * @var  string
	 */
	private $homeDirectory = null;

	/**
	 * Description for 'loginShell'
	 *
	 * @var  string
	 */
	private $loginShell = null;

	/**
	 * Description for 'ftpShell'
	 *
	 * @var  string
	 */
	private $ftpShell = null;

	/**
	 * Description for 'userPassword'
	 *
	 * @var  string
	 */
	private $userPassword = null;

	/**
	 * Description for 'shadowExpire'
	 *
	 * @var  integer
	 */
	private $shadowExpire = null;

	/**
	 * Description for 'gid'
	 *
	 * @var  string
	 */
	private $gid = null;

	/**
	 * Organization type
	 *
	 * @var  string
	 */
	private $orgtype = null;

	/**
	 * Organization
	 *
	 * @var  string
	 */
	private $organization = null;

	/**
	 * Two-letter code for 'Country of residence'
	 *
	 * @var  string
	 */
	private $countryresident = null;

	/**
	 * Two-letter code for 'Country of origin'
	 *
	 * @var  string
	 */
	private $countryorigin = null;

	/**
	 * Gender
	 *
	 * @var  string
	 */
	private $gender = null;

	/**
	 * URL
	 *
	 * @var  string
	 */
	private $url = null;

	/**
	 * Reason (for getting an account)
	 *
	 * @var  string
	 */
	private $reason = null;

	/**
	 * Receive email updates form the site?
	 *
	 * @var  integer
	 */
	private $mailPreferenceOption = -1;

	/**
	 * Agreed to Terms of Service?
	 *
	 * @var  integer
	 */
	private $usageAgreement = null;

	/**
	 * Timestamp for last time profile was modified
	 *
	 * @var  string
	 */
	private $modifiedDate = null;

	/**
	 * Email address confirmed?
	 *
	 * @var  integer
	 */
	private $emailConfirmed = null;

	/**
	 * Registration IP
	 *
	 * @var  string
	 */
	private $regIP = null;

	/**
	 * Registration host
	 *
	 * @var  string
	 */
	private $regHost = null;

	/**
	 * Native tribe
	 *
	 * @var  string
	 */
	private $nativeTribe = null;

	/**
	 * Phone number
	 *
	 * @var  string
	 */
	private $phone = null;

	/**
	 * Password from proxy creation
	 *
	 * @var  string
	 */
	private $proxyPassword = null;

	/**
	 * User ID from proxy creation
	 *
	 * @var  string
	 */
	private $proxyUidNumber = null;

	/**
	 * Given name (first name)
	 *
	 * @var  string
	 */
	private $givenName = null;

	/**
	 * Middle name
	 *
	 * @var  string
	 */
	private $middleName = null;

	/**
	 * Surname (last/family name)
	 *
	 * @var  string
	 */
	private $surname = null;

	/**
	 * Picture
	 *
	 * @var  string
	 */
	private $picture = null;

	/**
	 * VIP status?
	 *
	 * @var  integer
	 */
	private $vip = null;

	/**
	 * Public profile?
	 *
	 * @var  integer
	 */
	private $public = null;

	/**
	 * Parameters
	 *
	 * @var  string
	 */
	private $params = null;

	/**
	 * Notes
	 *
	 * @var  string
	 */
	private $note = null;

	/**
	 * ORCID
	 * http://orcid.org
	 *
	 * @var  string
	 */
	private $orcid = null;

	// Multi-value properties stored in auxilliary tables

	/**
	 * Profile bio
	 *
	 * @var  string
	 */
	private $_auxs_bio = null;

	/**
	 * List of diabilities
	 *
	 * @var  array
	 */
	private $_auxv_disability = array();

	/**
	 * List of hispanic options
	 *
	 * @var  array
	 */
	private $_auxv_hispanic = array();

	/**
	 * List of races
	 *
	 * @var  array
	 */
	private $_auxv_race = array();

	/**
	 * Description for '_auxv_admin'
	 *
	 * @var  array
	 */
	private $_auxv_admin = array();

	/**
	 * Description for '_auxv_host'
	 *
	 * @var  array
	 */
	private $_auxv_host = array();

	/**
	 * List of Edu levels
	 *
	 * @var  array
	 */
	private $_auxv_edulevel = array();

	/**
	 * List of roles
	 *
	 * @var  array
	 */
	private $_auxv_role = array();

	/**
	 * Description for '_password'
	 *
	 * @var  string
	 */
	private $_password = null;

	/**
	 * Registry
	 *
	 * @var  object
	 */
	private $_params = null;

	/**
	 * Add an error message.
	 *
	 * @param   string  $error  Error message.
	 * @param   string  $key    Specific key to set the value to
	 * @return  object
	 */
	public function setError($error, $key = null)
	{
		$bt = debug_backtrace();

		$error = __CLASS__ . '::' . $bt[1]['function'] . '():' . $error;

		return parent::setError($error, $key);
	}

	/**
	 * Reset all properties, making an empty profile object
	 *
	 * @return  boolean  True
	 */
	public function clear()
	{
		$classvars = get_class_vars(__CLASS__);

		foreach ($classvars as $property => $value)
		{
			if ('_s_' == substr($property, 0, 3)) // Don't touch static variables
			{
				continue;
			}

			unset($this->$property);
			$this->$property = $value;
		}

		$objvars = get_object_vars($this);

		foreach ($objvars as $property => $value)
		{
			if (!array_key_exists($property, $classvars))
			{
				unset($this->$property);
			}
		}

		return true;
	}

	/**
	 * Load a record from the MySQL database
	 *
	 * @param   mixed    $user  Integer (ID) or string (username)
	 * @return  boolean  True on success, False on error
	 */
	private function _mysql_load($user)
	{
		$db = \App::get('db');

		if (empty($user))
		{
			$this->setError('No user specified');
			return false;
		}

		// zooley: Removed check for >= 0 because profiles without acounts have negative IDs
		//if (is_numeric($user) && $user >= 0)
		if (is_numeric($user))
		{
			$query = "SELECT * FROM `#__xprofiles` WHERE uidNumber = " . $db->quote(intval($user)) . ";";
		}
		else
		{
			$query = "SELECT * FROM `#__xprofiles` WHERE username = " . $db->quote($user) . " AND uidNumber>0;";
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

		$this->_params = new Registry($result['params']);

		foreach ($result as $property => $value)
		{
			$this->set($property, $value);
		}

		$classvars = get_class_vars(__CLASS__);

		foreach ($classvars as $property => $value)
		{
			if ('_auxv_' == substr($property, 0, 6) || '_auxs_' == substr($property, 0, 6))
			{
				$this->$property = false; // This property is loaded on demand
			}
		}

		$this->_params->merge($this->params);

		return true;
	}

	/**
	 * Load an author record into this profile
	 *
	 * @param   integer  $authorid  Author ID
	 * @return  boolean  True on success, False on error
	 */
	private function _mysql_author_load($authorid)
	{
		static $_propertyauthormap = array(
			'uidNumber'    => 'id',
			'givenName'    => 'firstname',
			'middleName'   => 'middlename',
			'surname'      => 'lastname',
			'organization' => 'org',
			'bio'          => 'bio',
			'url'          => 'url',
			'picture'      => 'picture',
			'vip'          => 'principal_investigator'
		);

		$db =  \App::get('db');

		$query = "SELECT * FROM `#__author` WHERE id=" . $db->quote($authorid);

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
	 * Bind registration data
	 *
	 * @param   object  $registration
	 * @return  boolean
	 */
	private function _xregistration_load($registration)
	{
		static $_propertyregmap = array('username'=>'login', 'name'=>'name', 'email'=>'email', 'orcid'=>'orcid', 'orgtype'=>'orgtype', 'organization'=>'org', 'countryresident'=>'countryresident', 'countryorigin'=>'countryorigin', 'gender'=>'sex', 'url'=>'web', 'reason'=>'reason', 'mailPreferenceOption'=>'mailPreferenceOption', 'usageAgreement'=>'usageAgreement', 'nativeTribe'=>'nativeTribe', 'phone'=>'phone', 'disability'=>'disability', 'hispanic'=>'hispanic', 'race'=>'race', 'admin'=>'admin', 'host'=>'host', 'edulevel'=>'edulevel', 'role'=>'role', 'givenName'=>'givenName', 'middleName'=>'middleName', 'surname'=>'surname');

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
	 * Load registration data
	 *
	 * @param   object  &$registration
	 * @return  boolean
	 */
	public function loadRegistration(&$registration)
	{
		if (!is_object($registration))
		{
			return false;
		}

		$keys = array('email', 'name', 'orgtype', 'countryresident', 'countryorigin', 'disability', 'hispanic', 'race', 'phone', 'reason', 'edulevel', 'role', 'surname', 'givenName', 'middleName', 'orcid');

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
	 * Load a record
	 *
	 * @param   mixed    $user     User data
	 * @param   string   $storage  Storage type
	 * @return  boolean  True on success, False on error
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
	 * Constructor
	 *
	 * @param   mixed    $user  User data
	 * @return  boolean  True on success, False on error
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
	 * @param   mixed  $id  The user to load - Can be an integer or string
	 * @return  mixed  Returns object if valid record found, false if not
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
	 * Create a new entry in the profiles table
	 *
	 * @return  boolean  True on success, False on error
	 */
	public function create()
	{
		$db = \App::get('db');

		$modifiedDate = gmdate('Y-m-d H:i:s');

		if (is_numeric($this->get('uidNumber')))
		{
			$query = "INSERT INTO `#__xprofiles` (uidNumber,username,modifiedDate) VALUE (" . $db->quote($this->get('uidNumber')) . ',' . $db->quote($this->get('username')) . ',' . $db->quote($modifiedDate) . ");";

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

			$query = "INSERT INTO `#__xprofiles` (uidNumber,username,modifiedDate) SELECT " . "IF(MIN(uidNumber)>0,-1,MIN(uidNumber)-1)," . $db->quote($token) . ',' . $db->quote($modifiedDate) . " FROM #__xprofiles;";

			$db->setQuery($query);

			if (!$db->query())
			{
				$this->setError('Error inserting non-user data to xprofiles table: ' . $db->getErrorMsg());

				return false;
			}

			$query = "SELECT uidNumber FROM `#__xprofiles` WHERE username=" . $db->quote($token) . " AND modifiedDate=" . $db->quote($modifiedDate);

			$db->setQuery($query);

			$result = $db->loadColumn();

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
	 * Alias for the load() method
	 *
	 * @param   boolean  $instance  The user to load - Can be an integer or string
	 * @return  boolean  True on success, False on error
	 */
	public function read($instance = null)
	{
		return $this->load($instance);
	}

	/**
	 * Store data to the database record.
	 * Creates a record if doesn't exist, otherwise updates record.
	 *
	 * @return  boolean  True on success, False on error
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
	 * Update an existing record
	 *
	 * @return  boolean  True on success, False on error
	 */
	public function update()
	{
		if (!is_numeric($this->get('uidNumber')))
		{
			return false;
		}

		$db =  \App::get('db');

		$modifiedDate = gmdate('Y-m-d H:i:s');

		$this->set('modifiedDate', $modifiedDate);

		$query = "UPDATE `#__xprofiles` SET ";

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

			if ($property == 'params')
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
				$query .= "$property=" . $db->quote($this->get($property));
			}
		}

		$query .= " WHERE uidNumber=" . $db->quote($this->get('uidNumber')) . ";";

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

				$query_values .= '(' . $db->quote($this->get('uidNumber')) . ',' . $db->quote($value) . ')';
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
					$list[$key] = $db->quote($value);
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
			Event::trigger('user.onAfterStoreProfile', array($this));
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @return  boolean  True on success, False on error
	 */
	public function delete()
	{
		$db = \App::get('db');

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

			$query = "DELETE FROM `#__xprofiles_$property` WHERE uidNumber = " . $db->quote($this->get('uidNumber'));
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

		$query = "DELETE FROM `#__xprofiles` WHERE uidNumber = " . $db->quote($this->get('uidNumber'));
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
			Event::trigger('user.onAfterDeleteProfile', array($this));
		}

		$this->clear();

		return true;
	}

	/**
	 * Get a property's value
	 *
	 * @param   string  $property  Name of the property to retrieve
	 * @param   mixed   $value     Default value
	 * @return  mixed
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
			$db = \App::get('db');

			$property_name = substr($property, 6);

			$query = "SELECT $property_name FROM `#__xprofiles` AS x, `#__xprofiles_$property_name` AS xp WHERE x.uidNumber=xp.uidNumber AND xp.uidNumber=" . $db->quote($this->get('uidNumber')) . " ORDER BY $property_name ASC;";

			$db->setQuery($query);

			$result = $db->loadColumn();

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
	 * Set a property's value
	 *
	 * @param   string   $property  Property name
	 * @param   mixed    $value     Property value
	 * @return  boolean  True on success, False on error
	 */
	public function set($property, $value = null)
	{
		if ($property == 'password')
		{
			if ($value != '')
			{
				$this->userPassword = \Hubzero\User\Password::getPasshash($value);
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
	 * Add to a list of values for multi-value properties
	 *
	 * @param   string   $property  Property name
	 * @param   array    $value     Property values
	 * @return  boolean  True on success, False on error
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
	 * Remove from a list of values for multi-value properties
	 *
	 * @param   string   $property  Property name
	 * @param   array    $value     Property values
	 * @return  boolean  True on success, False on error
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
	 * Returns a property of the Params object or 
	 * the default value if the property is not set.
	 *
	 * @param   string   $key      The name of the property.
	 * @param   mixed    $default  The default value.
	 * @return  boolean
	 */
	public function getParam($key, $default = null)
	{
		return $this->_params->get($key, $default);
	}

	/**
	 * Modifies a property of the Params object.
	 *
	 * @param   string   $key    The name of the property.
	 * @param   mixed    $value  The value of the property to set.
	 * @return  boolean
	 */
	public function setParam($key, $value)
	{
		return $this->_params->set($key, $value);
	}

	/**
	 * Sets a default value on the Params object
	 * if not alreay assigned.
	 *
	 * @param   string   $key    The name of the property.
	 * @param   mixed    $value  The default value.
	 * @return  boolean
	 */
	public function defParam($key, $value)
	{
		return $this->_params->def($key, $value);
	}

	/**
	 * Get parameters object
	 *
	 * @param   boolean  $loadsetupfile  Load the XML set up file?
	 * @param   string   $path           Path to parameters XML file
	 * @return  object   Registry
	 */
	public function &getParameters($loadsetupfile = false, $path = null)
	{
		static $parampath;

		/*
		// Set a custom parampath if defined
		if (isset($path))
		{
			$parampath = $path;
		}

		// Set the default parampath if not set already
		if (!isset($parampath))
		{
			$parampath = PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'admin' . DS . 'models';
		}

		if ($loadsetupfile)
		{
			$type = str_replace(' ', '_', strtolower($this->usertype));

			$file = $parampath . DS . $type . '.xml';
			if (!file_exists($file))
			{
				$file = $parampath . DS . 'user.xml';
			}

			$this->_params->loadSetupFile($file);
		}
		*/

		return $this->_params;
	}

	/**
	 * Set parameters
	 *
	 * @param   object  $params  Parameters object to set
	 * @return  void
	 */
	public function setParameters($params)
	{
		$this->_params = $params;
	}

	/**
	 * Get group roles for a specific member/group pair
	 *
	 * @param   string  $uid  User ID
	 * @param   string  $gid  Group ID
	 * @return  array
	 */
	public static function getGroupMemberRoles($uid, $gid)
	{
		$db = \App::get('db');
		$sql = "SELECT r.id, r.name, r.permissions FROM `#__xgroups_roles` as r, `#__xgroups_member_roles` as m WHERE r.id=m.roleid AND m.uidNumber=" . $db->quote($uid) . " AND r.gidNumber=" . $db->quote($gid);
		$db->setQuery($sql);

		return $db->loadAssocList();
	}

	/**
	 * Check to see if user has permission to perform task
	 *
	 * @param   object   $group   \Hubzero\User\Group
	 * @param   string   $action  Group Action to perform
	 * @return  boolean
	 */
	public static function userHasPermissionForGroupAction($group, $action)
	{
		// Get user roles
		$roles = self::getGroupMemberRoles(
			\User::get('id'),
			$group->get('gidNumber')
		);

		// Check to see if any of our roles for user has permission for action
		foreach ($roles as $role)
		{
			$permissions = json_decode($role['permissions']);
			$permissions = (is_object($permissions)) ? $permissions : new \stdClass;
			if (property_exists($permissions, $action) && $permissions->$action == 1)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the groups for a user
	 *
	 * @param   string  $role  The group set to return. Returns all groups if not set
	 * @return  array   Array of groups
	 */
	public function getGroups($role = 'all')
	{
		static $groups;

		if (!isset($groups))
		{
			$groups = array(
				'applicants' => array(),
				'invitees'   => array(),
				'members'    => array(),
				'managers'   => array(),
				'all'        => array()
			);
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
	 * @param   string   $as       Format to return state in [text, number]
	 * @param   integer  $shorten  Number of characters to shorten text to
	 * @return  string
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

				Event::trigger('content.onContentPrepare', array(
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
	 * @param   integer  $anonymous  Is user anonymous?
	 * @param   boolean  $thumbit    Show thumbnail or full picture?
	 * @return  string
	 */
	public function getPicture($anonymous=0, $thumbit=true, $serveFile=true)
	{
		return ProfileHelper::getMemberPhoto($this, $anonymous, $thumbit, $serveFile);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function getLink($type='')
	{
		if (!$id = $this->get('uidNumber'))
		{
			return '';
		}

		$link = 'index.php?option=com_members&id=' . $id;

		// If it doesn't exist or isn't published
		$type = strtolower($type);
		switch ($type)
		{
			case 'edit':
			case 'changepassword':
				$link .= '&task=' . $type;
			break;

			default:
			break;
		}

		return $link;
	}
}
