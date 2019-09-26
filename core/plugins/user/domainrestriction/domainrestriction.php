<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * User plugin for blocking registration by domains or IPs
 */
class plgUserDomainRestriction extends Hubzero\Plugin\Plugin
{
	public $_tlds;
	public $_domains;
	public $_emails;
	public $_badtlds;
	public $_baddomains;
	public $_bademails;
	public $_email;
	public $_domain;
	public $_tld;
	public $_allowed;
	public $_gmp;

	/**
	 * Constructor
	 *
	 * @param   object  $subject
	 * @param   array   $config
	 * @return  void
	 */
	public function __construct(&$subject, $config = array())
	{
		$this->_gmp = function_exists('gmp_pow');

		parent::__construct($subject, $config);
	}

	/**
	 * Utility method to act on a user before it has been saved.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @return  bool
	 */
	public function onUserBeforeSave($user, $isNew, $new)
	{
		if (App::isAdmin())
		{
			return true;
		}

		if ($isNew)
		{
			$listtest = $this->_blackwhite($this->_getIP());

			if ($listtest === true)
			{
				return true;
			}

			if ($listtest !== false)
			{
				Notify::warning(Lang::txt('PLG_USER_DOMAINRESTRICTION_DENY'));
				return false;
			}
		}
		else
		{
			if ($this->params->get('ignorechange', 1))
			{
				return true;
			}
		}

		$result = true;

		// retrieve and clean up domain and email params
		$this->_sortParams();
		$this->_parseEmail($new['email']);

		//var_dump($this->_domains); die;

		// Here is the logic: allowed takes precedence over disallowed: if the domain/email is both allowed and disallowed it will be allowed. If the domain/email both neither allowed, nor disallowed it will be allowed.
		// I'm open to suggestions

		// Presumption of innocence: allowed, unless disallowed
		$this->_allowed = true;

		// Check if top level domain is disallowed
		if (in_array($this->_tld, $this->_badtlds))
		{
			$this->_allowed = false;
		}

		// Check if domain is disallowed
		if (in_array($this->_domain, $this->_baddomains))
		{
			$this->_allowed = false;
		}

		// Check if domain is allowed (may reverse two previous steps, and that is ok)
		if (in_array($this->_domain, $this->_domains)) {
			$this->_allowed = true;
		}

		// Check if email is disallowed
		if (in_array($this->_email, $this->_bademails))
		{
			$this->_allowed = false;
		}

		// Check if email is allowed
		if (in_array($this->_email, $this->_emails))
		{
			$this->_allowed = true;
		}

		// Check for affiliated registration and let it go
		if ($this->_domain == 'invalid')
		{
			$this->_allowed = true;
		}

		if (!$this->_allowed)
		{
			Lang::load('plg_user_domainrestriction', __DIR__);

			$message = $isNew ? 'PLG_USER_DOMAINRESTRICTION_DENY' : 'PLG_USER_DOMAINRESTRICTION_DENYCHANGE';
			Notify::error(Lang::txt($message));

			$result = false;
		}

		return $result;
	}

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method assigns the user to access groups based on plugin settings
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 * @return  void
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if ($isnew)
		{
			$this->_updateGroups($user);
		}

		return true;
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array    $user     holds the user data
	 * @param   array    $options  array holding options (remember, autoregister, group)
	 * @return  boolean  True on success
	 */
	public function onUserLogin($user, $options)
	{
		$this->_updateGroups($user);
		return true;
	}

	/**
	 * Get the user's IP
	 *
	 * @return  string
	 */
	private function _getIP()
	{
		return Request::ip();
	}

	/**
	 * Check user's IP against whitelist/blacklist
	 *
	 * @param   string  $ip
	 * @return  mixed
	 */
	private function _blackwhite($ip)
	{
		$whitelistnet = array();
		$blacklistnet = array();
		$whitelist = array();
		$blacklist = array();

		foreach (array('whitelist', 'blacklist') as $list)
		{
			$listnet = $list . 'net';
			$listdefault = ($list == 'blacklist') ? Lang::txt('PLG_USER_DOMAINRESTRICTION_DEFAULT_BLACKLIST') : Lang::txt('PLG_USER_DOMAINRESTRICTION_DEFAULT_WHITELIST');
			$$list = explode("\n", trim(str_replace(array("\r", "\t", " "), array('', '', ''), $this->params->get($list, $listdefault))));

			foreach ($$list as $key => $item)
			{
				$item = trim($item);
				if (preg_match('/\//', $item))
				{
					unset(${$list}[$key]);
					array_push($$listnet, $item);
				}
				else
				{
					${$list}[$key] = $item;
				}
			}
		}

		if (in_array($ip, $whitelist))
		{
			return true;
		}

		if (in_array($ip, $blacklist))
		{
			return $ip;
		}

		if (count(array_merge($whitelistnet, $blacklistnet)))
		{
			$require = $this->_gmp ? 'IPv6Net' : 'SimpleCIDR';

			if (!class_exists($require))
			{
				require_once __DIR__ . '/helpers/' . $require . '.php';
			}

			foreach ($whitelistnet as $net)
			{
				$ipnet = $this->_bwnet($net);

				if ($ipnet->contains($ip))
				{
					return true;
				}
			}

			foreach ($blacklistnet as $net)
			{
				$ipnet = $this->_bwnet($net);

				if ($ipnet->contains($ip))
				{
					return $net;
				}
			}
		}
		return false;
	}

	/**
	 * Match email
	 *
	 * @param   string  $net
	 * @return  object
	 */
	private function _bwnet($net)
	{
		return $this->_gmp ? (new IPv6Net($net)) : SimpleCIDR::getInstance($net);
	}

	/**
	 * Match email
	 *
	 * @param   bool  $allowed
	 * @return  bool
	 */
	private function _decision($allowed = false)
	{
		$ret = $allowed ?
				$this->_mailmatch(array('_tlds', '_domains', '_emails')) :
				!$this->_mailmatch(array('_badtlds', '_baddomains', '_bademails'));
		return $ret;
	}

	/**
	 * Match email address
	 *
	 * @param   array  $keys
	 * @return  bool
	 */
	private function _mailmatch($keys = array())
	{
		$ret = false;

		//print_r($keys); die;

		if ($this->{$keys[0]} || $this->{$keys[1]} || $this->{$keys[2]})
		{
			if (in_array($this->_tld, $this->{$keys[0]})
			 || in_array($this->_domain, $this->{$keys[1]})
			 || in_array($this->_email, $this->{$keys[2]}))
			{
				$ret = true;
			}
		}

		//echo '-->'; var_dump($ret); die('oops');

		return $ret;
	}

	/**
	 * Sort params
	 *
	 * @return  void
	 */
	private function _sortParams()
	{
		foreach (array('tld', 'domain', 'email', 'badtld', 'baddomain', 'bademail') as $param)
		{
			$paramvalue = $this->params->get($param, null);
			$arrayvar = '_' . $param . 's';

			$this->{$arrayvar} = array();
			$this->{$arrayvar} = array_map(array($this, 'clean'), explode(',', $paramvalue));

			foreach ($this->{$arrayvar} as $key => $d)
			{
				if (!strlen(trim($d)))
				{
					unset($this->{$arrayvar[$key]});
				}
			}
		}
	}

	private static function clean($val)
	{
	    return trim($val, " \t\n\r\0\x0B.");
	}

	/**
	 * Parse email address
	 *
	 * @param   string  $email
	 * @return  string
	 */
	private function _parseEmail($email)
	{
		$this->_email = strtolower($email);

		$email = explode('@', $this->_email);

		$this->_domain = $email[1];
		$this->_tld = $this->_getTldFromUrl($email[1]);

		return $email[0];
	}

	/**
	 * Update a user's groups
	 *
	 * @param   array  $user
	 * @return  bool
	 */
	private function _updateGroups($user)
	{
		if (App::isAdmin())
		{
			return true;
		}

		// Are there any auto-group assignments?
		$assignments = $this->_getAssignments();
		if (!$assignments)
		{
			return true;
		}

		$user = $this->_getUser($user['username']);

		$excludegroups = $this->params->get('excludegroup', array());
		foreach ($user->groups as $group)
		{
			if (in_array($group, (array) $excludegroups))
			{
				return true;
			}
		}

		$emailuser = $this->_parseEmail($user->email);
		$excluded = json_decode(str_replace('*', $emailuser, base64_decode($this->params->get('excludeauto', 'W10K'))));

		if (count($excluded) && in_array(strtolower($user->email), $excluded))
		{
			return true;
		}

		$akey = $this->_getAssignmentsKey($assignments);

		if ($akey)
		{
			$groupchange = false;
			foreach ($assignments[$akey] as $groupid)
			{
				if (!in_array($groupid, $user->groups))
				{
					Hubzero\Access\Map::addUserToGroup($user->id, $groupid);
					$groupchange = true;
				}
			}
			foreach ($user->groups as $groupid)
			{
				if (!in_array($groupid, $assignments[$akey]))
				{
					Hubzero\Access\Map::removeUserFromGroup($user->id, $groupid);
					$groupchange = true;
				}
			}

			if ($groupchange)
			{
				$user->set('groups', Hubzero\Access\Access::getGroupsByUser($user->id));
				$user->set('authlevels', Hubzero\Access\Access::getAuthorisedViewLevels($user->id));
			}
		}
		return true;
	}

	/**
	 * Get user by username
	 *
	 * @param   string  $username
	 * @return  object
	 */
	private function _getUser($username)
	{
		return User::getInstance($username);
	}

	/**
	 * Get assignments
	 *
	 * @return  mixed
	 */
	private function _getAssignments()
	{
		$assignments = json_decode(base64_decode($this->params->get('autogroups', 'W10K')));
		if (count($assignments))
		{
			foreach ($assignments as $key => $assignment)
			{
				if (is_array($assignment))
				{
					$assignments[strtolower($assignment[0])] = $assignment[1];
				}
				else
				{
					$assignments[strtolower($assignment->domain)] = $assignment->groups;
				}
				unset($assignments[$key]);
			}
		}
		else
		{
			$assignments = false;
		}
		return $assignments;
	}

	/**
	 * Get assignment key
	 *
	 * @param   array  $assignments
	 * @return  mixed
	 */
	private function _getAssignmentsKey($assignments)
	{
		return array_key_exists($this->_email, $assignments) ? $this->_email : (
				array_key_exists($this->_domain, $assignments) ? $this->_domain : (
				array_key_exists($this->_tld, $assignments) ? $this->_tld : false
			)
		);
	}

	/**
	 * Get tld from URL
	 *
	 * @param   string  $url
	 * @return  string
	 */
	private function _getTldFromUrl($url)
	{
		$url = strpos($url, '://') ? $url : 'http://' . $url;

		$host = parse_url($url);
		$domain = str_replace('__', '', $host['host']);
		$tail = strlen($domain) >= 7 ? substr($domain, -7) : $domain;
		$tld = strstr($tail, '.');

		return preg_replace('/^\./', '', $tld);
	}
}
