<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2013 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2009-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Utility;

/**
 * Hubzero helper class for input validation
 *
 * Several methods inspired by CakePHP (http://cakephp.org) and Zend (http://framework.zend.com)
 */
class Validate
{
	/**
	 * Some complex patterns needed in multiple places
	 *
	 * @var array
	 */
	protected static $_pattern = array(
		'hostname' => '(?:[_a-z0-9][-_a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})'
	);

	/**
	 * Checks that a string contains something other than whitespace
	 *
	 * Returns true if string contains something other than whitespace
	 *
	 * $check can be passed as an array:
	 * array('check' => 'valueToCheck');
	 *
	 * @param  string|array $check Value to check
	 * @return boolean Success
	 */
	public static function notEmpty($check)
	{
		if (is_array($check))
		{
			extract(self::_defaults($check));
		}

		if (empty($check) && $check != '0')
		{
			return false;
		}
		return self::_check($check, '/[^\s]+/m');
	}

	/**
	 * Checks that a string contains only integer or letters
	 *
	 * Returns true if string contains only integer or letters
	 *
	 * $check can be passed as an array:
	 * array('check' => 'valueToCheck');
	 *
	 * @param  string|array $check Value to check
	 * @return boolean Success
	 */
	public static function alphaNumeric($check)
	{
		if (is_array($check))
		{
			extract(self::_defaults($check));
		}

		if (empty($check) && $check != '0')
		{
			return false;
		}
		return self::_check($check, '/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]+$/Du');
	}

	/**
	 * Checks that a string length is within s specified range.
	 * Spaces are included in the character count.
	 * Returns true is string matches value min, max, or between min and max,
	 *
	 * @param string $check Value to check for length
	 * @param integer $min Minimum value in range (inclusive)
	 * @param integer $max Maximum value in range (inclusive)
	 * @return boolean Success
	 */
	public static function between($check, $min, $max)
	{
		$length = mb_strlen($check);
		return ($length >= $min && $length <= $max);
	}

	/**
	 * Returns true if field is left blank -OR- only whitespace characters are present in its value
	 * Whitespace characters include Space, Tab, Carriage Return, Newline
	 *
	 * $check can be passed as an array:
	 * array('check' => 'valueToCheck');
	 *
	 * @param string|array $check Value to check
	 * @return boolean Success
	 */
	public static function blank($check)
	{
		if (is_array($check))
		{
			extract(self::_defaults($check));
		}
		return !self::_check($check, '/[^\\s]/');
	}

	/**
	 * Boolean validation, determines if value passed is a boolean integer or true/false.
	 *
	 * @param string $check a valid boolean
	 * @return boolean Success
	 */
	public static function boolean($check)
	{
		$booleanList = array(0, 1, '0', '1', true, false);
		return in_array($check, $booleanList, true);
	}

	/**
	 * Validate that a number is in specified range.
	 * if $lower and $upper are not set, will return true if
	 * $check is a legal finite on this platform
	 *
	 * @param string $check Value to check
	 * @param integer $lower Lower limit
	 * @param integer $upper Upper limit
	 * @return boolean Success
	 */
	public static function range($check, $lower = null, $upper = null)
	{
		if (!is_numeric($check))
		{
			return false;
		}
		if (isset($lower) && isset($upper))
		{
			return ($check > $lower && $check < $upper);
		}
		return is_finite($check);
	}

	/**
	 * Checks if a value is numeric.
	 *
	 * @param string $check Value to check
	 * @return boolean Success
	 */
	public static function numeric($check)
	{
		return is_numeric($check);
	}

	/**
	 * Is value an integer?
	 *
	 * @param      unknown $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function integer($x)
	{
		return (self::numeric($x) && intval($x) == $x);
	}

	/**
	 * Is value a positive integer?
	 *
	 * @param      integer $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function positiveInteger($x)
	{
		return (self::integer($x) && $x > 0);
	}

	/**
	 * Is value a non-negative integer?
	 *
	 * @param      integer $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function nonNegativeInteger($x)
	{
		return (self::integer($x) && $x >= 0);
	}

	/**
	 * Is value a non-positive integer?
	 *
	 * @param      integer $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function nonPositiveInteger($x)
	{
		return (self::integer($x) && $x <= 0);
	}

	/**
	 * Is value a negative integer?
	 *
	 * @param      integer $x Value to check
	 * @return     boolean True if valid, false if invalid
	 */
	static public function negativeInteger($x)
	{
		return (self::integer($x) && $x < 0);
	}

	/**
	 * Validate ORCID
	 *
	 * @param      string $orcid ORCID to validate
	 * @return     boolean True if valid, false if invalid
	 */
	public static function orcid($orcid)
	{
		if (preg_match('#^[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}$#i', $orcid))
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if a username is valid.
	 *
	 * - Check if the username contains any invalid characters
	 * - Check if username is just an integer
	 * - Check if the username is reserved
	 *
	 * @param  string $x Username to check
	 * @return boolean True if valid, false if invalid
	 */
	public static function username($x)
	{
		// Does it contain invalid characters?
		if (!preg_match("/^[0-9a-zA-Z]+[_0-9a-zA-Z]*$/i", $x))
		{
			return false;
		}

		// Is it a positive integer?
		if (self::nonNegativeInteger($x))
		{
			return false;
		}

		// Is it a reserved username?
		if (self::reserved('username', $x))
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if a group alias is valid
	 *
	 * - Check if $cn contains any invalid characters
	 * - Check if $cn is just an integer
	 * - Check if $cn is reserved
	 *
	 * @param  string  $cn          Group to check
	 * @param  boolean $allowDashes Allow dashes in cn
	 * @return boolean True if valid, false if invalid
	 */
	static public function group($cn, $allowDashes = false)
	{
		$pattern = '/^[0-9a-zA-Z]+[' . ($allowDashes ? '-' : '') . '_0-9a-zA-Z]*$/i';

		if (!preg_match($pattern, $cn))
		{
			return false;
		}

		if (self::nonNegativeInteger($cn))
		{
			return false;
		}

		// Is it a reserved group name?
		if (self::reserved('group', $cn))
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if $val is reserved
	 *
	 * Type [username, group] must be specified.
	 *
	 * @param  string  $type List to check against
	 * @param  string  $val  Value to check
	 * @return boolean True if reserved, False if not
	 * @throws InvalidArgumentException
	 */
	static public function reserved($type, $val)
	{
		static $reserved = array(
			'username'  => array(
				'adm',
				'alfred',
				'apache',
				'backup',
				'bin',
				'canna',
				'condor',
				'condor-util',
				'daemon',
				'debian-exim',
				'exim',
				'ftp',
				'games',
				'ganglia',
				'gnats',
				'gopher',
				'gridman',
				'halt',
				'httpd',
				'ibrix',
				'invigosh',
				'irc',
				'ldap',
				'list',
				'lp',
				'mail',
				'mailnull',
				'man',
				'mysql',
				'nagios',
				'netdump',
				'news',
				'nfsnobody',
				'noaccess',
				'nobody',
				'nscd',
				'ntp',
				'operator',
				'openldap',
				'pcap',
				'postgres',
				'proxy',
				'pvm',
				'root',
				'rpc',
				'rpcuser',
				'rpm',
				'sag',
				'shutdown',
				'smmsp',
				'sshd',
				'statd',
				'sync',
				'sys',
				'submit',
				'uucp',
				'vncproxy',
				'vncproxyd',
				'vcsa',
				'wheel',
				'www',
				'www-data',
				'xfs',
			),
			'group' => array(
				'abrt',
				'adm',
				'apache',
				'apps',
				'audio',
				'avahi',
				'avahi-autoipd',
				'backup',
				'bin',
				'boinc',
				'cdrom',
				'cgred',
				'cl-builder',
				'clamav',
				'condor',
				'crontab',
				'ctapiusers',
				'daemon',
				'dbus',
				'debian-exim',
				'desktop_admin_r',
				'desktop_user_r',
				'dialout',
				'dip',
				'disk',
				'fax',
				'floppy',
				'ftp',
				'fuse',
				'games',
				'gdm',
				'gnats',
				'gopher',
				'gridman',
				'haldaemon',
				'hsqldb',
				'irc',
				'itisunix',
				'jackuser',
				'kmem',
				'kvm',
				'ldap',
				'libuuid',
				'list',
				'lock',
				'lp',
				'mail',
				'man',
				'mem',
				'messagebus',
				'mysql',
				'netdev',
				'news',
				'nfsnobody',
				'nobody',
				'nogroup',
				'nscd',
				'nslcd',
				'ntp',
				'openldap',
				'operator',
				'oprofile',
				'plugdev',
				'postdrop',
				'postfix',
				'powerdev',
				'proxy',
				'pulse',
				'pulse-access',
				'qemu',
				'qpidd',
				'radvd',
				'rdma',
				'root',
				'rpc',
				'rpcuser',
				'rtkit',
				'sasl',
				'saslauth',
				'shadow',
				'slocate',
				'src',
				'ssh',
				'sshd',
				'ssl-cert',
				'staff',
				'stapdev',
				'stapusr',
				'stap-server',
				'stapsys',
				'stunnel4',
				'sudo',
				'sys',
				'tape',
				'tcpdump',
				'tomcat',
				'tty',
				'tunnelers',
				'usbmuxd',
				'users',
				'utmp',
				'utempter',
				'uucp',
				'video',
				'vcsa',
				'voice',
				'wbpriv',
				'webalizer',
				'wheel',
				'www-data',
				'zookeeper',
			)
		);

		$type = strtolower(trim($type));

		if (!isset($reserved[$type]))
		{
			throw new \InvalidArgumentException(\JText::sprintf('Type must be "username" or "group". Type of "%s" provided.', $type));
		}

		if (in_array(strtolower($val), $reserved[$type]))
		{
			return true;
		}

		return false;
	}

	/**
	 * Validate password
	 *
	 * @param      string $password Password to validate
	 * @return     boolean True if valid, false if invalid
	 */
	public function password($password)
	{
		if (preg_match("#^[_\`\~\!\@\#\$\%\^\&\*\(\)\=\+\{\}\:\;\"\'\<\>\,\.\?\/0-9a-zA-Z-]+$#i", $password))
		{
			return true;
		}
		return false;
	}

	/**
	 * Validates for an email address.
	 *
	 * Only uses getmxrr() checking for deep validation if PHP 5.3.0+ is used, or
	 * any PHP version on a non-windows distribution
	 *
	 * @param  string  $check Value to check
	 * @param  boolean $deep  Perform a deeper validation (if true), by also checking availability of host
	 * @param  string  $regex Regex to use (if none it will use built in regex)
	 * @return boolean Success
	 */
	public static function email($check, $deep = false, $regex = null)
	{
		if (is_array($check))
		{
			extract(self::_defaults($check));
		}

		if ($regex === null)
		{
			$regex = '/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@' . self::$_pattern['hostname'] . '$/i';
		}
		$return = self::_check($check, $regex);
		if ($deep === false || $deep === null)
		{
			return $return;
		}

		if ($return === true && preg_match('/@(' . self::$_pattern['hostname'] . ')$/i', $check, $regs))
		{
			if (function_exists('getmxrr') && getmxrr($regs[1], $mxhosts))
			{
				return true;
			}
			if (function_exists('checkdnsrr') && checkdnsrr($regs[1], 'MX'))
			{
				return true;
			}
			return is_array(gethostbynamel($regs[1]));
		}
		return false;
	}

	/**
	 * Validation of an IP address.
	 *
	 * @param string $check The string to test.
	 * @param string $type The IP Protocol version to validate against
	 * @return boolean Success
	 */
	public static function ip($check, $type = 'both')
	{
		$type = strtolower($type);
		$flags = 0;
		if ($type === 'ipv4')
		{
			$flags = FILTER_FLAG_IPV4;
		}
		if ($type === 'ipv6')
		{
			$flags = FILTER_FLAG_IPV6;
		}
		return (boolean)filter_var($check, FILTER_VALIDATE_IP, array('flags' => $flags));
	}

	/**
	 * Checks that a value is a valid URL according to http://www.w3.org/Addressing/URL/url-spec.txt
	 *
	 * The regex checks for the following component parts:
	 *
	 * - a valid, optional, scheme
	 * - a valid ip address OR
	 *   a valid domain name as defined by section 2.3.1 of http://www.ietf.org/rfc/rfc1035.txt
	 *   with an optional port number
	 * - an optional valid path
	 * - an optional query string (get parameters)
	 * - an optional fragment (anchor tag)
	 *
	 * @param  string  $check  Value to check
	 * @param  boolean $strict Require URL to be prefixed by a valid scheme (one of http(s)/ftp(s)/file/news/gopher)
	 * @return boolean Success
	 */
	public static function url($check, $strict = false)
	{
		self::_populateIp();

		$validChars = '([' . preg_quote('!"$&\'()*+,-.@_:;=~[]|') . '\/0-9a-z\p{L}\p{N}]|(%[0-9a-f]{2}))';

		$regex = '/^(?:(?:https?|ftps?|sftp|file|news|gopher):\/\/)' . (!empty($strict) ? '' : '?') .
			'(?:' . self::$_pattern['IPv4'] . '|\[' . self::$_pattern['IPv6'] . '\]|' . self::$_pattern['hostname'] . ')(?::[1-9][0-9]{0,4})?' .
			'(?:\/?|\/' . $validChars . '*)?' .
			'(?:\?' . $validChars . '*)?' .
			'(?:#' . $validChars . '*)?$/iu';

		return self::_check($check, $regex);
	}

	/**
	 * Check that a value is a valid phone number.
	 *
	 * @param  string|array $check   Value to check (string or array)
	 * @param  string       $regex   Regular expression to use
	 * @param  string       $country Country code (defaults to 'all')
	 * @return boolean Success
	 */
	public static function phone($check, $regex = null, $country = 'all')
	{
		if (is_array($check))
		{
			extract(self::_defaults($check));
		}

		if ($regex === null)
		{
			switch ($country)
			{
				case 'us':
				case 'ca':
				case 'can': // deprecated three-letter-code
				case 'all':
					// includes all NANPA members.
					// see http://en.wikipedia.org/wiki/North_American_Numbering_Plan#List_of_NANPA_countries_and_territories
					$regex = '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?';

					// Area code 555, X11 is not allowed.
					$areaCode = '(?![2-9]11)(?!555)([2-9][0-8][0-9])';
					$regex .= '(?:\(\s*' . $areaCode . '\s*\)|' . $areaCode . ')';
					$regex .= '\s*(?:[.-]\s*)?)';

					// Exchange and 555-XXXX numbers
					$regex .= '(?!(555(?:\s*(?:[.\-\s]\s*))(01([0-9][0-9])|1212)))';
					$regex .= '(?!(555(01([0-9][0-9])|1212)))';
					$regex .= '([2-9]1[02-9]|[2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)';

					// Local number and extension
					$regex .= '?([0-9]{4})';
					$regex .= '(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/';
				break;
			}
		}
		if (empty($regex))
		{
			return self::_pass('phone', $check, $country);
		}
		return self::_check($check, $regex);
	}

	/**
	 * Attempts to pass unhandled Validation locales to a class starting with $classPrefix
	 * and ending with Validation. For example $classPrefix = 'nl', the class would be
	 * `NlValidation`.
	 *
	 * @param  string $method      The method to call on the other class.
	 * @param  mixed  $check       The value to check or an array of parameters for the method to be called.
	 * @param  string $classPrefix The prefix for the class to do the validation.
	 * @return mixed Return of Passed method, false on failure
	 */
	protected static function _pass($method, $check, $classPrefix)
	{
		$className = ucwords($classPrefix) . 'Validation';
		if (!class_exists($className))
		{
			trigger_error(JText::sprintf('Could not find %s class, unable to complete validation.', $className), E_USER_WARNING);
			return false;
		}
		if (!method_exists($className, $method))
		{
			trigger_error(JText::sprintf('Method %s does not exist on %s unable to complete validation.', $method, $className), E_USER_WARNING);
			return false;
		}
		$check = (array)$check;
		return call_user_func_array(array($className, $method), $check);
	}

	/**
	 * Runs a regular expression match.
	 *
	 * @param string $check Value to check against the $regex expression
	 * @param string $regex Regular expression
	 * @return boolean Success of match
	 */
	protected static function _check($check, $regex)
	{
		if (is_string($regex) && preg_match($regex, $check))
		{
			return true;
		}
		return false;
	}

	/**
	 * Get the values to use when value sent to validation method is
	 * an array.
	 *
	 * @param  array $params Parameters sent to validation method
	 * @return void
	 */
	protected static function _defaults($params)
	{
		self::_reset();
		$defaults = array(
			'check'   => null,
			'regex'   => null,
			'country' => null,
			'deep'    => false,
			'type'    => null
		);
		$params = array_merge($defaults, $params);
		if ($params['country'] !== null)
		{
			$params['country'] = mb_strtolower($params['country']);
		}
		return $params;
	}

	/**
	 * Lazily populate the IP address patterns used for validations
	 *
	 * @return void
	 */
	protected static function _populateIp()
	{
		if (!isset(self::$_pattern['IPv6']))
		{
			$pattern  = '((([0-9A-Fa-f]{1,4}:){7}(([0-9A-Fa-f]{1,4})|:))|(([0-9A-Fa-f]{1,4}:){6}';
			$pattern .= '(:|((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})';
			$pattern .= '|(:[0-9A-Fa-f]{1,4})))|(([0-9A-Fa-f]{1,4}:){5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})';
			$pattern .= '(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)';
			$pattern .= '{4}(:[0-9A-Fa-f]{1,4}){0,1}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2}))';
			$pattern .= '{3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){0,2}';
			$pattern .= '((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|';
			$pattern .= '((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}';
			$pattern .= '((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2}))';
			$pattern .= '{3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)(:[0-9A-Fa-f]{1,4})';
			$pattern .= '{0,4}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)';
			$pattern .= '|((:[0-9A-Fa-f]{1,4}){1,2})))|(:(:[0-9A-Fa-f]{1,4}){0,5}((:((25[0-5]|2[0-4]';
			$pattern .= '\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4})';
			$pattern .= '{1,2})))|(((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?';

			self::$_pattern['IPv6'] = $pattern;
		}
		if (!isset(self::$_pattern['IPv4']))
		{
			$pattern = '(?:(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|(?:(?:1[0-9])?|[1-9]?)[0-9])';
			self::$_pattern['IPv4'] = $pattern;
		}
	}

	/**
	 * Reset internal variables for another validation run.
	 *
	 * @return void
	 */
	protected static function _reset()
	{
		self::$errors = array();
	}
}

