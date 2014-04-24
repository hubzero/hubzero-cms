<?php
/**
 * HUBzero CMS
 *
 * Copyright 2010-2012 Purdue University. All rights reserved.
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
 * @copyright Copyright 2010-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\User;

use Hubzero\User\Password\History;

/**
 * Password handling class for users
 */
class Password
{
	/**
	 * Description for 'user_id'
	 *
	 * @var mixed
	 */
	private $user_id = null;
	
	/**
	 * Description for 'passhash'
	 *
	 * @var unknown
	 */
	private $passhash = null;
	
	/**
	 * Description for 'shadowLastChange'
	 *
	 * @var unknown
	 */
	private $shadowLastChange = null;
	
	/**
	 * Description for 'shadowMin'
	 *
	 * @var array
	 */
	private $shadowMin = array();
	
	/**
	 * Description for 'shadowMax'
	 *
	 * @var unknown
	 */
	private $shadowMax = null;
	
	/**
	 * Description for 'shadowWarning'
	 *
	 * @var unknown
	 */
	private $shadowWarning = null;
	
	/**
	 * Description for 'shadowInactive'
	 *
	 * @var unknown
	 */
	private $shadowInactive = null;
	
	/**
	 * Description for 'shadowExpire'
	 *
	 * @var unknown
	 */
	private $shadowExpire = null;
	
	/**
	 * Description for 'shadowFlag'
	 *
	 * @var unknown
	 */
	private $shadowFlag = null;
	
	/**
	 * Description for '_updatedkeys'
	 *
	 * @var array
	 */
	private $_updatedkeys = array();

	/**
	 * Short description for '__construct' Long description (if any) . ..
	 *
	 * @return void
	 */
	private function __construct()
	{
	}

	/**
	 * Short description for 'clear' Long description (if any) . ..
	 *
	 * @return void
	 */
	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);
		
		$this->_updatedkeys = array();
		
		foreach ($cvars as $key=>$value)
		{
			if ($key{0} != '_')
			{
				unset($this->$key);
				
				$this->$key = null;
			}
		}
		
		$this->_updatedkeys = array();
	}

	/**
	 * Short description for 'getInstance' Long description (if any) . ..
	 *
	 * @param unknown $instance Parameter description (if any) ...
	 * @param unknown $storage Parameter description (if any) ...
	 * @return mixed Return description (if any) ...
	 */
	public static function getInstance($instance, $storage = null)
	{
		$hzup = new self();
		
		if ($hzup->read($instance) === false)
		{
			return false;
		}
		
		return $hzup;
	}

	/**
	 * Short description for 'create' Long description (if any) . ..
	 *
	 * @return boolean Return description (if any) ...
	 */
	public function create()
	{
		$db =  \JFactory::getDBO();
		
		if (empty($db))
		{
			return false;
		}
		
		// @FIXME: this should fail if id doesn't exist in #__users
		

		if ($this->user_id > 0)
		{
			$query = "INSERT INTO #__users_password (user_id) VALUES ( " . $db->Quote($this->user_id) . ");";
			
			$db->setQuery($query);
			
			$result = $db->query();
			
			if ($result !== false || $db->getErrorNum() == 1062)
			{
				return true;
			}
			
			$this->update();
		}
		
		return false;
	}

	/**
	 * Short description for 'read' Long description (if any) . ..
	 *
	 * @param integer $user_id Parameter description (if any) ...
	 * @param string $storage Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function read($instance = null)
	{
		if (empty($instance))
		{
			$instance = $this->user_id;
		}
		
		if (empty($instance))
		{
			return false;
		}
		
		$this->clear();
		
		$db = \JFactory::getDBO();
		
		if (empty($db))
		{
			return false;
		}
		
		$result = true;
		
		if (is_numeric($instance))
		{
			if ($instance <= 0)
			{
				return false;
			}
			
			$query = "SELECT user_id,passhash,shadowLastChange,shadowMin," . "shadowMax,shadowWarning,shadowInactive,shadowExpire," . "shadowFlag FROM #__users_password WHERE user_id=" . $db->Quote($instance) . ";";
		}
		else
		{
			$query = "SELECT user_id,passhash,shadowLastChange,shadowMin," . "shadowMax,shadowWarning,shadowInactive,shadowExpire," . "shadowFlag FROM #__users_password,#__users WHERE user_id=id" . " AND username=" . $db->Quote($instance) . ";";
		}
		
		$db->setQuery($query);
		
		$result = $db->loadAssoc();
		
		if (!empty($result))
		{
			foreach ($result as $key=>$value)
			{
				$this->__set($key, $value);
			}
			
			$this->_updatedkeys = array();
		}
		else
		{
			$hzp = Profile::getInstance($instance);
			
			if (is_object($hzp))
			{
				$this->__set('user_id', $hzp->get('uidNumber'));
				$this->__set('passhash', $hzp->get('userPassword'));
				$this->create();
			}
			else
			{
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Short description for 'update' Long description (if any) . ..
	 *
	 * @param boolean $all Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public function update()
	{
		$db = \JFactory::getDBO();
		
		$query = "UPDATE #__users_password SET ";
		
		$classvars = get_class_vars(__CLASS__);
		
		$first = true;
		
		foreach ($classvars as $property=>$value)
		{
			if (($property{0} == '_'))
			{
				continue;
			}
			
			if (!in_array($property, $this->_updatedkeys))
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
			
			$value = $this->__get($property);
			
			if ($value === null)
			{
				$query .= "`$property`=NULL";
			}
			else
			{
				$query .= "`$property`=" . $db->Quote($value);
			}
		}
		
		$query .= " WHERE `user_id`=" . $db->Quote($this->__get('user_id')) . " LIMIT 1;";
		
		if ($first == true)
		{
			$query = '';
		}
		
		$affected = 0;
		if (!empty($query))
		{
			$db->setQuery($query);
			
			$result = $db->query();
			
			if ($result)
			{
				$affected = $db->getAffectedRows();
			}
		}
		
		if ($affected > 0)
		{
			\JPluginHelper::importPlugin('user');
			\JDispatcher::getInstance()->trigger('onAfterStorePassword', array($this));
		}
		
		return true;
	}

	/**
	 * Short description for 'delete' Long description (if any) . ..
	 *
	 * @return boolean Return description (if any) ...
	 */
	public function delete()
	{
		if ($this->user_id <= 0)
		{
			return false;
		}
		
		$db = \JFactory::getDBO();
		
		if (empty($db))
		{
			return false;
		}
		
		if (!isset($this->user_id))
		{
			$db->setQuery("SELECT user_id FROM #__users_password WHERE user_id" . $db->Quote($this->user_id) . ";");
			
			$this->__set('user_id', $db->loadResult());
		}
		
		if (empty($this->user_id))
		{
			return false;
		}
		
		$db->setQuery("DELETE FROM #__users_password WHERE user_id= " . $db->Quote($this->user_id) . ";");
		
		$affected = 0;
		
		if ($db->query())
		{
			$affected = $db->getAffectedRows();
		}
		
		if ($affected > 0)
		{
			\JPluginHelper::importPlugin('user');
			\JDispatcher::getInstance()->trigger('onAfterDeletePassword', array($this));
		}
		
		return true;
	}

	/**
	 * Short description for '__get' Long description (if any) . ..
	 *
	 * @param string $property Parameter description (if any) ...
	 * @return string Return description (if any) ...
	 */
	public function __get($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}
			
			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}
		
		if (isset($this->$property))
		{
			return $this->$property;
		}
		
		if (array_key_exists($property, get_object_vars($this)))
		{
			return null;
		}
		
		$this->_error("Undefined property " . __CLASS__ . "::$" . $property, E_USER_NOTICE);
		
		return null;
	}

	/**
	 * Short description for '__set' Long description (if any) . ..
	 *
	 * @param string $property Parameter description (if any) ...
	 * @param unknown $value Parameter description (if any) ...
	 * @return void
	 */
	public function __set($property = null, $value = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}
			
			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}
		
		$this->$property = $value;
		
		if (!in_array($property, $this->_updatedkeys))
		{
			$this->_updatedkeys[] = $property;
		}
	}

	/**
	 * Short description for '__isset' Long description (if any) . ..
	 *
	 * @param string $property Parameter description (if any) ...
	 * @return string Return description (if any) ...
	 */
	public function __isset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}
			
			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}
		
		return isset($this->$property);
	}

	/**
	 * Short description for '__unset' Long description (if any) . ..
	 *
	 * @param string $property Parameter description (if any) ...
	 * @return void
	 */
	public function __unset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property{0} == '_')
		{
			if (empty($property))
			{
				$property = '(null)';
			}
			
			$this->_error("Cannot access property " . __CLASS__ . "::$" . $property, E_USER_ERROR);
			die();
		}
		
		$this->_updatedkeys = array_diff($this->_updatedkeys, array($property));
		
		unset($this->$property);
	}

	/**
	 * Short description for '_error' Long description (if any) . ..
	 *
	 * @param string $message Parameter description (if any) ...
	 * @param integer $level Parameter description (if any) ...
	 * @return void
	 */
	private function _error($message, $level = E_USER_NOTICE)
	{
		$caller = next(debug_backtrace());
		
		switch ($level)
		{
			case E_USER_NOTICE:
				echo "Notice: ";
				break;
			case E_USER_ERROR:
				echo "Fatal error: ";
				break;
			default:
				echo "Unknown error: ";
				break;
		}
		
		echo $message . ' in ' . $caller['file'] . ' on line ' . $caller['line'] . "\n";
	}

	/**
	 * Short description for 'get' Long description (if any) . ..
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @return unknown Return description (if any) ...
	 */
	public function get($key)
	{
		return $this->__get($key);
	}

	/**
	 * Short description for 'set' Long description (if any) . ..
	 *
	 * @param unknown $key Parameter description (if any) ...
	 * @param unknown $value Parameter description (if any) ...
	 * @return unknown Return description (if any) ...
	 */
	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	public static function isPasswordExpired($user = null)
	{
		$hzup = self::getInstance($user);
		
		if (!is_object($hzup))
		{
			return false;
		}
		
		if (empty($hzup->shadowLastChange))
		{
			return false;
		}
		
		if ($hzup->shadowMax === '0')
		{
			return true;
		}
		
		if (empty($hzup->shadowMax))
		{
			return false;
		}
		
		$chgtime = time();
		$chgtime = intval($chgtime / 86400);
		
		if (($hzup->shadowLastChange + $hzup->shadowMax) >= $chgtime)
		{
			return false;
		}
		
		return true;
	}

	public static function getPasshash($password)
	{
		// Get the password encryption/hashing mechanism
		$config = \JComponentHelper::getParams('com_members');
		$type   = $config->get('passhash_mechanism', 'CRYPT_SHA512');

		switch ($type)
		{
			case 'MD5':
				$passhash = "{MD5}" . base64_encode(pack('H*', md5($password)));
				break;

			case 'CRYPT_SHA512':
			default:
				$encrypted = crypt($password, '$6$' . \JUserHelper::genRandomPassword(8) . '$');
				$passhash = "{CRYPT}" . $encrypted;
				break;
		}

		return $passhash;
	}

	public static function changePassword($user = null, $password)
	{
		$passhash = self::getPasshash($password);

		return self::changePasshash($user, $passhash);
	}

	public static function changePasshash($user = null, $passhash)
	{
		// Get config values for min, max, and warning
		$config        = \JComponentHelper::getParams('com_members');
		$shadowMin     = $config->get('shadowMin', '0');
		$shadowMax     = $config->get('shadowMax', null);
		$shadowWarning = $config->get('shadowWarning', '7');

		// Translate empty shadowMax to mean NULL
		$shadowMax = ($shadowMax == '') ? null : $shadowMax;

		$hzup = self::getInstance($user);

		$oldhash = $hzup->__get('passhash');

		$hzup->__set('passhash', $passhash);
		$hzup->__set('shadowFlag', null);
		$hzup->__set('shadowLastChange', intval(time() / 86400));
		$hzup->__set('shadowMin', $shadowMin);
		$hzup->__set('shadowMax', $shadowMax);
		$hzup->__set('shadowWarning', $shadowWarning);
		$hzup->__set('shadowInactive', '0');
		$hzup->__set('shadowExpire', null);
		$hzup->update();

		$db = \JFactory::getDBO();

		$db->setQuery("UPDATE `#__xprofiles` SET userPassword=" . $db->Quote($passhash) . " WHERE uidNumber=" . $db->Quote($hzup->get('user_id')));
		$db->query();

		$db->setQuery("UPDATE `#__users` SET password=" . $db->Quote($passhash) . " WHERE id=" . $db->Quote($hzup->get('user_id')));
		$db->query();

		if (!empty($oldhash))
		{
			History::addPassword($oldhash, $user);
		}

		return true;
	}

	public static function comparePasswords($passhash, $password)
	{
		if (empty($passhash) || empty($password))
		{
			return false;
		}

		preg_match("/^\s*(\{(.*)\}\s*|)((.*?)\s*:\s*|)(.*?)\s*$/", $passhash, $matches);

		$encryption = strtolower($matches[2]);

		if (empty($encryption))
		{ // Joomla
			$encryption = "md5-hex";

			if (!empty($matches[4]))
			{ // Joomla 1.5
				$crypt = $matches[4];
				$salt  = $matches[5];
			}
			else
			{ // Joomla 1.0
				$crypt = $matches[5];
				$salt  = '';
			}
		}
		else
		{
			$salt  = $matches[4];
			$crypt = $matches[5];
		}

		if ($encryption == 'md5')
		{
			$encryption = "md5-base64";
		}
		else if ($encryption == 'crypt')
		{
			preg_match('/\$([[:alnum:]]{1,2})\$[[:alnum:]]{8}\$/', $passhash, $parts);
			$salt = $parts[0];

			switch ($parts[1])
			{
				case '6':
				default:
					$encryption = 'crypt-sha512';
					break;
			}
		}

		if (empty($salt) && ($encryption == 'ssha'))
		{
			$salt = substr(base64_decode(substr($crypt, -32)), -4);
			$hashed = base64_encode(mhash(MHASH_SHA1, $password . $salt) . $salt);
		}
		else
		{
			jimport('joomla.user.helper');
			$hashed = \JUserHelper::getCryptedPassword($password, $salt, $encryption);
		}

		return ($crypt == $hashed);
	}

	public static function passwordMatches($user = null, $password, $alltables = false)
	{
		$passhash = null;

		$hzup = self::getInstance($user);

		if (is_object($hzup) && !empty($hzup->passhash))
		{
			$passhash = $hzup->passhash;
		}
		else if ($alltables)
		{
			$profile = Profile::getInstance($user);

			if (is_object($profile) && ($profile->get('userPassword') != ''))
			{
				$passhash = $profile->get('userPassword');
			}
			else
			{
				$user = \JUser::getInstance($user);

				if (is_object($user) && !empty($user->password))
				{
					$passhash = $user->password;
				}
			}
		}

		return self::comparePasswords($passhash, $password);
	}

	public static function invalidatePassword($user = null)
	{
		$hzup = self::getInstance($user);

		$hzup->__set('shadowFlag', '-1');
		$hzup->update();

		return true;
	}

	public static function expirePassword($user = null)
	{
		$hzup = self::getInstance($user);

		$hzup->__set('shadowLastChange', '1');
		$hzup->__set('shadowMax', '0');
		$hzup->update();

		return true;
	}
}
