<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @var  mixed
	 */
	private $user_id = null;

	/**
	 * Description for 'passhash'
	 *
	 * @var  string
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
	 * @var  array
	 */
	private $_updatedkeys = array();

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	private function __construct()
	{
	}

	/**
	 * Clear internal data
	 *
	 * @return  void
	 */
	public function clear()
	{
		$cvars = get_class_vars(__CLASS__);

		$this->_updatedkeys = array();

		foreach ($cvars as $key => $value)
		{
			if ($key[0] != '_')
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
	 * @param   unknown $instance Parameter description (if any) ...
	 * @param   unknown $storage Parameter description (if any) ...
	 * @return  mixed Return description (if any) ...
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
	 * Create a record
	 *
	 * @return  boolean
	 */
	public function create()
	{
		$db =  \App::get('db');

		if (empty($db))
		{
			return false;
		}

		// @FIXME: this should fail if id doesn't exist in #__users
		if ($this->user_id > 0)
		{
			$query = "INSERT INTO #__users_password (user_id) VALUES ( " . $db->quote($this->user_id) . ");";

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
	 * Read a record
	 *
	 * @param   integer  $instance
	 * @return  boolean
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

		$db = \App::get('db');

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

			$query = "SELECT user_id,passhash,shadowLastChange,shadowMin," . "shadowMax,shadowWarning,shadowInactive,shadowExpire," . "shadowFlag FROM #__users_password WHERE user_id=" . $db->quote($instance) . ";";
		}
		else
		{
			$query = "SELECT user_id,passhash,shadowLastChange,shadowMin," . "shadowMax,shadowWarning,shadowInactive,shadowExpire," . "shadowFlag FROM #__users_password,#__users WHERE user_id=id" . " AND username=" . $db->quote($instance) . ";";
		}

		$db->setQuery($query);

		$result = $db->loadAssoc();

		if (!empty($result))
		{
			foreach ($result as $key => $value)
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
	 * Update a record
	 *
	 * @return  boolean
	 */
	public function update()
	{
		$db = \App::get('db');

		if (!$this->__get('user_id'))
		{
			return false;
		}

		$query = "UPDATE `#__users_password` SET ";

		$classvars = get_class_vars(__CLASS__);

		$first = true;

		foreach ($classvars as $property => $value)
		{
			if (($property[0] == '_'))
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
				$query .= "`$property`=" . $db->quote($value);
			}
		}

		$query .= " WHERE `user_id`=" . $db->quote($this->__get('user_id'));

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
			\Event::trigger('user.onAfterStorePassword', array($this));
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		if ($this->user_id <= 0)
		{
			return false;
		}

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (!isset($this->user_id))
		{
			$db->setQuery("SELECT user_id FROM `#__users_password` WHERE user_id" . $db->quote($this->user_id) . ";");

			$this->__set('user_id', $db->loadResult());
		}

		if (empty($this->user_id))
		{
			return false;
		}

		$db->setQuery("DELETE FROM `#__users_password` WHERE user_id= " . $db->quote($this->user_id) . ";");

		$affected = 0;

		if ($db->query())
		{
			$affected = $db->getAffectedRows();
		}

		if ($affected > 0)
		{
			\Event::trigger('user.onAfterDeletePassword', array($this));
		}

		return true;
	}

	/**
	 * Get a property's value
	 *
	 * @param   string  $property
	 * @return  mixed
	 */
	public function __get($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property[0] == '_')
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
	 * Set a property's value
	 *
	 * @param   string  $property
	 * @param   mixed   $value
	 * @return  void
	 */
	public function __set($property = null, $value = null)
	{
		if (!property_exists(__CLASS__, $property) || $property[0] == '_')
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
	 * Check if a property is set
	 *
	 * @param   string  $property
	 * @return  bool
	 */
	public function __isset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property[0] == '_')
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
	 * Unset a property
	 *
	 * @param   string  $property
	 * @return  void
	 */
	public function __unset($property = null)
	{
		if (!property_exists(__CLASS__, $property) || $property[0] == '_')
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
	 * Output an error message
	 *
	 * @param   string   $message
	 * @param   integer  $level
	 * @return  void
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
	 * Get a property's value
	 *
	 * @param   string  $property
	 * @return  mixed
	 */
	public function get($key)
	{
		return $this->__get($key);
	}

	/**
	 * Set a property's value
	 *
	 * @param   string  $property
	 * @param   mixed   $value
	 * @return  void
	 */
	public function set($key, $value)
	{
		return $this->__set($key, $value);
	}

	/**
	 * Check if a password is expired
	 *
	 * @param   mixed  $user
	 * @return  bool
	 */
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

	/**
	 * Get a hash of a password
	 *
	 * @param   string  $password
	 * @return  string
	 */
	public static function getPasshash($password)
	{
		// Get the password encryption/hashing mechanism
		$config = \Component::params('com_members');
		$type   = $config->get('passhash_mechanism', 'CRYPT_SHA512');

		switch ($type)
		{
			case 'MD5':
				$passhash = "{MD5}" . base64_encode(pack('H*', md5($password)));
				break;

			case 'CRYPT_SHA512':
			default:
				$encrypted = crypt($password, '$6$' . self::genRandomPassword(8) . '$');
				$passhash = "{CRYPT}" . $encrypted;
				break;
		}

		return $passhash;
	}

	/**
	 * Generate a random password
	 *
	 * @param   integer  $length  Length of the password to generate
	 * @return  string   Random Password
	 */
	public static function genRandomPassword($length = 8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$base = strlen($salt);
		$makepass = '';

		// Start with a cryptographic strength random string, then convert it to
		// a string with the numeric base of the salt.
		// Shift the base conversion on each character so the character
		// distribution is even, and randomize the start shift so it's not
		// predictable.
		$random = \Hubzero\Encryption\Encrypter::genRandomBytes($length + 1);
		$shift = ord($random[0]);
		for ($i = 1; $i <= $length; ++$i)
		{
			$makepass .= $salt[($shift + ord($random[$i])) % $base];
			$shift += ord($random[$i]);
		}

		return $makepass;
	}

	/**
	 * Change a user's password
	 *
	 * @param   mixed   $user
	 * @param   string  $password
	 * @return  bool
	 */
	public static function changePassword($user = null, $password)
	{
		$passhash = self::getPasshash($password);

		return self::changePasshash($user, $passhash);
	}

	/**
	 * Change a user's pass hash
	 *
	 * @param   mixed   $user
	 * @param   string  $password
	 * @return  bool
	 */
	public static function changePasshash($user = null, $passhash)
	{
		// Get config values for min, max, and warning
		$config        = \Component::params('com_members');
		$shadowMin     = $config->get('shadowMin', '0');
		$shadowMax     = $config->get('shadowMax', null);
		$shadowWarning = $config->get('shadowWarning', '7');

		// Translate empty shadowMax to mean NULL
		$shadowMax = ($shadowMax == '') ? null : $shadowMax;

		$hzup = self::getInstance($user);

		// Make sure we found a record
		if (!$hzup)
		{
			return false;
		}

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

		$db = \App::get('db');

		$db->setQuery("UPDATE `#__xprofiles` SET userPassword=" . $db->quote($passhash) . " WHERE uidNumber=" . $db->quote($hzup->get('user_id')));
		$db->query();

		$db->setQuery("UPDATE `#__users` SET password=" . $db->quote($passhash) . " WHERE id=" . $db->quote($hzup->get('user_id')));
		$db->query();

		if (!empty($oldhash))
		{
			History::addPassword($oldhash, $user);
		}

		return true;
	}

	/**
	 * Compare passwords
	 *
	 * @param   string  $passhash
	 * @param   string  $password
	 * @return  bool
	 */
	public static function comparePasswords($passhash, $password)
	{
		if (empty($passhash) || empty($password))
		{
			return false;
		}

		preg_match("/^\s*(\{(.*)\}\s*|)((.*?)\s*:\s*|)(.*?)\s*$/", $passhash, $matches);

		$encryption = strtolower($matches[2]);

		if (empty($encryption))
		{
			// Joomla
			$encryption = "md5-hex";

			if (!empty($matches[4]))
			{
				// Joomla 1.5
				$crypt = $matches[4];
				$salt  = $matches[5];
			}
			else
			{
				// Joomla 1.0
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
			$hashed = self::_getCryptedPassword($password, $salt, $encryption);
		}

		return ($crypt == $hashed);
	}

	/**
	 * Formats a password using the current encryption.
	 *
	 * @param   string   $plaintext     The plaintext password to encrypt.
	 * @param   string   $salt          The salt to use to encrypt the password. If not present, a new salt will be generated.
	 * @param   string   $encryption    The kind of password encryption to use. Defaults to md5-hex.
	 * @param   boolean  $show_encrypt  Some password systems prepend the kind of encryption to the crypted password ({SHA}, etc). Defaults to false.
	 * @return  string   The encrypted password.
	 */
	protected static function _getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
	{
		// Get the salt to use.
		$salt = self::getSalt($encryption, $salt, $plaintext);

		// Encrypt the password.
		switch ($encryption)
		{
			case 'plain':
				return $plaintext;

			case 'sha':
				$encrypted = base64_encode(hash('SHA1', $plaintext, true));
				return ($show_encrypt) ? '{SHA}' . $encrypted : $encrypted;

			case 'crypt':
			case 'crypt-des':
			case 'crypt-md5':
			case 'crypt-blowfish':
			case 'crypt-sha512':
				return ($show_encrypt ? '{crypt}' : '') . crypt($plaintext, $salt);

			case 'md5-base64':
				$encrypted = base64_encode(hash('MD5', $plaintext, true));
				return ($show_encrypt) ? '{MD5}' . $encrypted : $encrypted;

			case 'ssha':
				$encrypted = base64_encode(hash('SHA1', $plaintext . $salt, true) . $salt);
				return ($show_encrypt) ? '{SSHA}' . $encrypted : $encrypted;

			case 'smd5':
				$encrypted = base64_encode(hash('MD5', $plaintext . $salt, true) . $salt);
				return ($show_encrypt) ? '{SMD5}' . $encrypted : $encrypted;

			case 'aprmd5':
				$length = strlen($plaintext);
				$context = $plaintext . '$apr1$' . $salt;
				$binary = self::_bin(md5($plaintext . $salt . $plaintext));

				for ($i = $length; $i > 0; $i -= 16)
				{
					$context .= substr($binary, 0, ($i > 16 ? 16 : $i));
				}
				for ($i = $length; $i > 0; $i >>= 1)
				{
					$context .= ($i & 1) ? chr(0) : $plaintext[0];
				}

				$binary = self::_bin(md5($context));

				for ($i = 0; $i < 1000; $i++)
				{
					$new = ($i & 1) ? $plaintext : substr($binary, 0, 16);
					if ($i % 3)
					{
						$new .= $salt;
					}
					if ($i % 7)
					{
						$new .= $plaintext;
					}
					$new .= ($i & 1) ? substr($binary, 0, 16) : $plaintext;
					$binary = self::_bin(md5($new));
				}

				$p = array();
				for ($i = 0; $i < 5; $i++)
				{
					$k = $i + 6;
					$j = $i + 12;
					if ($j == 16)
					{
						$j = 5;
					}
					$p[] = self::_toAPRMD5((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
				}

				return '$apr1$' . $salt . '$' . implode('', $p) . self::_toAPRMD5(ord($binary[11]), 3);

			case 'md5-hex':
			default:
				$encrypted = ($salt) ? md5($plaintext . $salt) : md5($plaintext);
				return ($show_encrypt) ? '{MD5}' . $encrypted : $encrypted;
		}
	}

	/**
	 * Returns a salt for the appropriate kind of password encryption.
	 * Optionally takes a seed and a plaintext password, to extract the seed
	 * of an existing password, or for encryption types that use the plaintext
	 * in the generation of the salt.
	 *
	 * @param   string  $encryption  The kind of password encryption to use. Defaults to md5-hex.
	 * @param   string  $seed        The seed to get the salt from (probably a previously generated password). Defaults to generating a new seed.
	 * @param   string  $plaintext   The plaintext password that we're generating  a salt for. Defaults to none.
	 * @return  string  The generated or extracted salt.
	 */
	public static function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '')
	{
		// Encrypt the password.
		switch ($encryption)
		{
			case 'crypt':
			case 'crypt-des':
				if ($seed)
				{
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 2);
				}
				else
				{
					return substr(md5(mt_rand()), 0, 2);
				}
			break;

			case 'crypt-md5':
				if ($seed)
				{
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
				}
				else
				{
					return '$1$' . substr(md5(mt_rand()), 0, 8) . '$';
				}
			break;

			case 'crypt-blowfish':
				if ($seed)
				{
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 16);
				}
				else
				{
					return '$2$' . substr(md5(mt_rand()), 0, 12) . '$';
				}
			break;

			case 'crypt-sha512':
				if ($seed)
				{
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
				}
				else
				{
					return '$6$' . substr(md5(mt_rand()), 0, 8) . '$';
				}
			break;

			case 'ssha':
				if ($seed)
				{
					return substr(preg_replace('|^{SSHA}|', '', $seed), -20);
				}
				else
				{
					return mhash_keygen_s2k(MHASH_SHA1, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
			break;

			case 'smd5':
				if ($seed)
				{
					return substr(preg_replace('|^{SMD5}|', '', $seed), -16);
				}
				else
				{
					return mhash_keygen_s2k(MHASH_MD5, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
			break;

			case 'aprmd5':
				/* 64 characters that are valid for APRMD5 passwords. */
				$APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

				if ($seed)
				{
					return substr(preg_replace('/^\$apr1\$(.{8}).*/', '\\1', $seed), 0, 8);
				}
				else
				{
					$salt = '';
					for ($i = 0; $i < 8; $i++)
					{
						$salt .= $APRMD5[rand(0, 63)];
					}
					return $salt;
				}
			break;

			default:
				$salt = '';
				if ($seed)
				{
					$salt = $seed;
				}
				return $salt;
			break;
		}
	}

	/**
	 * Converts to allowed 64 characters for APRMD5 passwords.
	 *
	 * @param   string   $value  The value to convert.
	 * @param   integer  $count  The number of characters to convert.
	 * @return  string   $value  converted to the 64 MD5 characters.
	 */
	protected static function _toAPRMD5($value, $count)
	{
		// 64 characters that are valid for APRMD5 passwords.
		$APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		$aprmd5 = '';
		$count = abs($count);
		while (--$count)
		{
			$aprmd5 .= $APRMD5[$value & 0x3f];
			$value >>= 6;
		}
		return $aprmd5;
	}

	/**
	 * Converts hexadecimal string to binary data.
	 *
	 * @param   string  $hex  Hex data.
	 * @return  string  Binary data.
	 */
	protected static function _bin($hex)
	{
		$bin = '';
		$length = strlen($hex);
		for ($i = 0; $i < $length; $i += 2)
		{
			$tmp = sscanf(substr($hex, $i, 2), '%x');
			$bin .= chr(array_shift($tmp));
		}
		return $bin;
	}

	/**
	 * Check if a password matches
	 *
	 * @param   mixed   $user
	 * @param   string  $password
	 * @param   bool    $alltables
	 * @return  bool
	 */
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
				$user = \User::getInstance($user);

				if (is_object($user) && !empty($user->password))
				{
					$passhash = $user->password;
				}
			}
		}

		return self::comparePasswords($passhash, $password);
	}

	/**
	 * Invalidate a user's password
	 *
	 * @param   mixed   $user
	 * @return  bool
	 */
	public static function invalidatePassword($user = null)
	{
		$hzup = self::getInstance($user);

		$hzup->__set('shadowFlag', '-1');
		$hzup->update();

		return true;
	}

	/**
	 * Expire a user's password
	 *
	 * @param   mixed   $user
	 * @return  bool
	 */
	public static function expirePassword($user = null)
	{
		$hzup = self::getInstance($user);

		$hzup->__set('shadowLastChange', '1');
		$hzup->__set('shadowMax', '0');
		$hzup->update();

		return true;
	}
}
