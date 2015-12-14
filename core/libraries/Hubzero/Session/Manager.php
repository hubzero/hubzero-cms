<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Session;

use Hubzero\Base\Object;

/**
 * Class for managing HTTP sessions
 *
 * Provides access to session-state values as well as session-level
 * settings and lifetime management methods.
 * Based on the standard PHP session handling mechanism it provides
 * more advanced features such as expire timeouts.
 *
 * Inspired by Joomla's JSession class
 */
class Manager extends Object
{
	/**
	 * Internal state.
	 * One of 'active'|'expired'|'destroyed'|'error'
	 *
	 * @var  string
	 */
	protected $state = 'active';

	/**
	 * Maximum age of unused session in minutes
	 *
	 * @var  string
	 */
	protected $expire = 15;

	/**
	 * The session store object.
	 *
	 * @var  object
	 */
	protected $store = null;

	/**
	 * Security policy.
	 * List of checks that will be done.
	 *
	 * Default values:
	 * - fix_browser
	 * - fix_adress
	 *
	 * @var  array
	 */
	protected $security = array('fix_browser');

	/**
	 * Force cookies to be SSL only
	 * Default  false
	 *
	 * @var  boolean
	 */
	protected $force_ssl = false;

	/**
	 * Cookie domain
	 *
	 * @var  string
	 */
	protected $cookie_domain = '';

	/**
	 * Cookie path
	 *
	 * @var  string
	 */
	protected $cookie_path = '/';

	/**
	 * Constructor
	 *
	 * @param   string  $store    The type of storage for the session.
	 * @param   array   $options  Optional parameters
	 */
	public function __construct($store = 'none', $options = array())
	{
		// Need to destroy any existing sessions started with session.auto_start
		if (session_id())
		{
			session_unset();
			session_destroy();
		}

		// Set default sessios save handler
		ini_set('session.save_handler', 'files');

		// Disable transparent sid support
		ini_set('session.use_trans_sid', '0');

		if ($store == 'database')
		{
			if (ini_get('session.gc_probability') < 1)
			{
				ini_set('session.gc_probability',1);
			}
			if (ini_get('session.gc_divisor') < 1)
			{
				ini_set('session.gc_divisor',100);
			}
		}

		// Create handler
		$this->store = Store::getInstance($store, $options);

		// Set options
		$this->setOptions($options);

		// Pass session id in query string when cookie not available.
		// This is used, in particular, to allow QuickTime plugin in Safari on the Mac
		// to view private mp4. QuickTime does not pass the browser's cookies to the site
		if (!isset($_COOKIE[session_name()]) && isset($_GET['PHPSESSID']))
		{
			if ((strlen($_GET['PHPSESSID']) == 32) && ctype_alnum($_GET['PHPSESSID']))
			{
				if ($this->store->read($_GET['PHPSESSID']) != '')
				{
					session_id($_GET['PHPSESSID']);
				}
			}
		}

		$this->setCookieParams();

		// Load the session
		$this->start();

		// Initialise the session
		$this->setCounter();
		$this->setTimers();

		$this->state = 'active';

		// Perform security checks
		$this->validate();
	}

	/**
	 * Session object destructor
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Get current state of session
	 *
	 * @return  string  The session state
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Get expiration time in minutes
	 *
	 * @return  integer  The session expiration time in minutes
	 */
	public function getExpire()
	{
		return $this->expire;
	}

	/**
	 * Get a session token, if a token isn't set yet one will be generated.
	 *
	 * Tokens are used to secure forms from spamming attacks. Once a token
	 * has been generated the system will check the post request to see if
	 * it is present, if not it will invalidate the session.
	 *
	 * @param   boolean  $forceNew  If true, force a new token to be created
	 * @return  string   The session token
	 */
	public function getToken($forceNew = false)
	{
		$token = $this->get('session.token');

		// Create a token
		if ($token === null || $forceNew)
		{
			$token = $this->createToken(12);

			$this->set('session.token', $token);
		}

		return $token;
	}

	/**
	 * Method to determine if a token exists in the session. If not the
	 * session will be set to expired
	 *
	 * @param   string   $tCheck       Hashed token to be verified
	 * @param   boolean  $forceExpire  If true, expires the session
	 * @return  boolean
	 */
	public function hasToken($tCheck, $forceExpire = true)
	{
		// Check if a token exists in the session
		$tStored = $this->get('session.token');

		// Check token
		if ($tStored !== $tCheck)
		{
			if ($forceExpire)
			{
				$this->state = 'expired';
			}

			return false;
		}

		return true;
	}

	/**
	 * Method to determine a hash for anti-spoofing variable names
	 *
	 * @param   boolean  $forceNew  If true, force a new token to be created
	 * @return  string   Hashed var name
	 */
	public static function getFormToken($forceNew = false)
	{
		$hash = \App::hash(\User::get('id', 0) . \App::get('session')->getToken($forceNew));

		return $hash;
	}

	/**
	 * Checks for a form token in the request.
	 *
	 * @param   string   $method   The request method in which to look for the token key.
	 * @param   boolean  $capture  Return result instead of throwing exception?
	 * @return  boolean  True if found and valid, false otherwise.
	 */
	public static function checkToken($method = 'post', $capture = false)
	{
		$token = self::getFormToken();

		$result = false;

		if (is_string($method) && strstr($method, ','))
		{
			$method = explode(',', $method);
			$method = array_map('trim', $method);
		}
		$method = (array) $method;

		foreach ($method as $m)
		{
			if (\App::get('request')->getVar($token, '', $m, 'alnum'))
			{
				$result = true;
				break;
			}

			if (\App::get('session')->isNew())
			{
				// Redirect to login screen.
				\App::redirect(\Route::url('index.php'), \App::get('language')->txt('JLIB_ENVIRONMENT_SESSION_EXPIRED'));
				\App::close();
			}
		}

		if (!$result)
		{
			if ($capture)
			{
				return $result;
			}

			\App::abort(403, \App::get('language')->txt('JINVALID_TOKEN'));
		}

		return $result;
	}

	/**
	 * Get session name
	 *
	 * @return  string  The session name
	 */
	public function getName()
	{
		if ($this->state === 'destroyed')
		{
			return null;
		}

		return session_name();
	}

	/**
	 * Get session id
	 *
	 * @return  string  The session name
	 */
	public function getId()
	{
		if ($this->state === 'destroyed')
		{
			return null;
		}

		return session_id();
	}

	/**
	 * Get the session handlers
	 *
	 * @return  array  An array of available session handlers
	 */
	public static function getStores()
	{
		$glob = glob(__DIR__ . DS . 'storage' . DS . '*');

		$names = array();

		if ($glob === false) return $names;

		$handlers = array_filter($glob, function($file)
		{
			return filetype($file) == 'file';
		});

		foreach ($handlers as $handler)
		{
			$name  = substr($handler, 0, strrpos($handler, '.'));
			$class = __NAMESPACE__ . '\\Storage\\' . ucfirst($name);

			if (!class_exists($class))
			{
				continue;
			}

			if (call_user_func_array(array(trim($class), 'isAvailable'), array()))
			{
				$names[] = $name;
			}
		}

		return $names;
	}

	/**
	 * Check whether this session is currently created
	 *
	 * @return  boolean  True on success.
	 */
	public function isNew()
	{
		if ($this->get('session.counter') === 1)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get data from the session store
	 *
	 * @param   string  $name       Name of a variable
	 * @param   mixed   $default    Default value of a variable if not set
	 * @param   string  $namespace  Namespace to use, default to 'default'
	 * @return  mixed   Value of a variable
	 */
	public function get($name, $default = null, $namespace = 'default')
	{
		// Add prefix to namespace to avoid collisions
		$namespace = '__' . $namespace;

		if ($this->state !== 'active' && $this->state !== 'expired')
		{
			// @TODO :: generated error here
			$error = null;

			return $error;
		}

		if (isset($_SESSION[$namespace][$name]))
		{
			return $_SESSION[$namespace][$name];
		}

		return $default;
	}

	/**
	 * Set data into the session store.
	 *
	 * @param   string  $name       Name of a variable.
	 * @param   mixed   $value      Value of a variable.
	 * @param   string  $namespace  Namespace to use, default to 'default'.
	 * @return  mixed   Old value of a variable.
	 */
	public function set($name, $value = null, $namespace = 'default')
	{
		// Add prefix to namespace to avoid collisions
		$namespace = '__' . $namespace;

		if ($this->state !== 'active')
		{
			// @TODO :: generated error here
			return null;
		}

		$old = isset($_SESSION[$namespace][$name]) ? $_SESSION[$namespace][$name] : null;

		if (null === $value)
		{
			unset($_SESSION[$namespace][$name]);
		}
		else
		{
			$_SESSION[$namespace][$name] = $value;
		}

		return $old;
	}

	/**
	 * Check whether data exists in the session store
	 *
	 * @param   string   $name       Name of variable
	 * @param   string   $namespace  Namespace to use, default to 'default'
	 * @return  boolean  True if the variable exists
	 */
	public function has($name, $namespace = 'default')
	{
		// Add prefix to namespace to avoid collisions.
		$namespace = '__' . $namespace;

		if ($this->state !== 'active')
		{
			// @TODO :: generated error here
			return null;
		}

		return isset($_SESSION[$namespace][$name]);
	}

	/**
	 * Unset data from the session store
	 *
	 * @param   string  $name       Name of variable
	 * @param   string  $namespace  Namespace to use, default to 'default'
	 * @return  mixed   The value from session or NULL if not set
	 */
	public function clear($name, $namespace = 'default')
	{
		// Add prefix to namespace to avoid collisions
		$namespace = '__' . $namespace;

		if ($this->state !== 'active')
		{
			// @TODO :: generated error here
			return null;
		}

		$value = null;
		if (isset($_SESSION[$namespace][$name]))
		{
			$value = $_SESSION[$namespace][$name];
			unset($_SESSION[$namespace][$name]);
		}

		return $value;
	}

	/**
	 * Start a session.
	 *
	 * Creates a session (or resumes the current one based on the state of the session)
	 *
	 * @return  boolean  true on success
	 */
	protected function start()
	{
		// Start session if not started
		if ($this->state == 'restart')
		{
			session_id($this->createId());
		}
		else
		{
			$session_name = session_name();

			if (!\Request::getVar($session_name, false, 'COOKIE'))
			{
				if ($id = \Request::getVar($session_name))
				{
					session_id($id);
					setcookie($session_name, '', time() - 3600);
				}
				else
				{
					session_id($this->createId());
				}
			}
		}

		session_cache_limiter('none');
		session_start();

		// Regenerate session id if passed a session id that no longer exists
		if ($_SESSION === array())
		{
			session_destroy();
			session_id($this->createId());
			session_start();
		}

		return true;
	}

	/**
	 * Frees all session variables and destroys all data registered to a session
	 *
	 * This method resets the $_SESSION variable and destroys all of the data associated
	 * with the current session in its storage (file or DB). It forces new session to be
	 * started after this method is called. It does not unset the session cookie.
	 *
	 * @return  boolean  True on success
	 * @see     session_destroy()
	 * @see     session_unset()
	 */
	public function destroy()
	{
		// Session was already destroyed
		if ($this->state === 'destroyed')
		{
			return true;
		}

		// In order to kill the session altogether, such as to log the user out, the session id
		// must also be unset. If a cookie is used to propagate the session id (default behavior),
		// then the session cookie must be deleted.
		if (isset($_COOKIE[session_name()]))
		{
			$cookie_domain = $this->cookie_domain;
			$cookie_path   = $this->cookie_path;

			setcookie(session_name(), '', time() - 42000, $cookie_path, $cookie_domain);
		}

		session_unset();
		session_destroy();

		$this->state = 'destroyed';

		return true;
	}

	/**
	 * Restart an expired or locked session.
	 *
	 * @return  boolean  True on success
	 * @see     destroy
	 */
	public function restart()
	{
		$this->destroy();

		if ($this->state !== 'destroyed')
		{
			// @TODO :: generated error here
			return false;
		}

		// Re-register the session handler after a session has been destroyed, to avoid PHP bug
		$this->store->register();

		$this->state = 'restart';

		// Regenerate session id
		$id = $this->createId();

		session_id($id);

		$this->start();
		$this->state = 'active';

		$this->validate();
		$this->setCounter();

		return true;
	}

	/**
	 * Create a new session and copy variables from the old one
	 *
	 * @return  boolean  $result  True on success
	 */
	public function fork()
	{
		if ($this->state !== 'active')
		{
			// @TODO :: generated error here
			return false;
		}

		// Save values
		$values = $_SESSION;

		// Keep session config
		$trans = ini_get('session.use_trans_sid');
		if ($trans)
		{
			ini_set('session.use_trans_sid', 0);
		}

		$cookie = session_get_cookie_params();

		// Create new session id
		$id = $this->createId();

		// Kill session
		session_destroy();

		// Re-register the session store after a session has been destroyed, to avoid PHP bug
		$this->store->register();

		// Restore config
		ini_set('session.use_trans_sid', $trans);
		session_set_cookie_params($cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure']);

		// Restart session with new id
		session_id($id);
		session_start();

		return true;
	}

	/**
	 * Writes session data and ends session
	 *
	 * Session data is usually stored after your script terminated without the need
	 * to call JSession::close(), but as session data is locked to prevent concurrent
	 * writes only one script may operate on a session at any time. When using
	 * framesets together with sessions you will experience the frames loading one
	 * by one due to this locking. You can reduce the time needed to load all the
	 * frames by ending the session as soon as all changes to session variables are
	 * done.
	 *
	 * @return  void
	 * @see     session_write_close()
	 */
	public function close()
	{
		session_write_close();
	}

	/**
	 * Create a session id
	 *
	 * @return  string  Session ID
	 */
	protected function createId()
	{
		$id = 0;

		while (strlen($id) < 32)
		{
			$id .= mt_rand(0, mt_getrandmax());
		}

		return md5(uniqid($id, true));
	}

	/**
	 * Set session cookie parameters
	 *
	 * @return  void
	 */
	protected function setCookieParams()
	{
		$cookie = session_get_cookie_params();

		if ($this->force_ssl)
		{
			$cookie['secure'] = true;
		}

		if ($this->cookie_domain != '')
		{
			$cookie['domain'] = $this->cookie_domain;
		}

		if ($this->cookie_path != '')
		{
			$cookie['path'] = $this->cookie_path;
		}

		session_set_cookie_params($cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure']);
	}

	/**
	 * Create a token-string
	 *
	 * @param   integer  $length  Length of string
	 * @return  string   Generated token
	 */
	protected function createToken($length = 32)
	{
		static $chars = '0123456789abcdef';

		$max   = strlen($chars) - 1;
		$token = '';
		$name  = session_name();

		for ($i = 0; $i < $length; ++$i)
		{
			$token .= $chars[(rand(0, $max))];
		}

		return md5($token . $name);
	}

	/**
	 * Set counter of session usage
	 *
	 * @return  boolean  True on success
	 */
	protected function setCounter()
	{
		$counter = $this->get('session.counter', 0);

		++$counter;

		$this->set('session.counter', $counter);

		return true;
	}

	/**
	 * Set the session timers
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	protected function setTimers()
	{
		if (!$this->has('session.timer.start'))
		{
			$start = time();

			$this->set('session.timer.start', $start);
			$this->set('session.timer.last', $start);
			$this->set('session.timer.now', $start);
		}

		$this->set('session.timer.last', $this->get('session.timer.now'));
		$this->set('session.timer.now', time());

		return true;
	}

	/**
	 * Set additional session options
	 *
	 * @param   array    $options  List of parameter
	 * @return  boolean  True on success
	 */
	protected function setOptions($options)
	{
		// Set name
		if (isset($options['name']))
		{
			session_name(md5($options['name']));
		}

		// Set id
		if (isset($options['id']))
		{
			session_id($options['id']);
		}

		// Set expire time
		if (isset($options['expire']))
		{
			$this->expire = $options['expire'];
		}

		// Get security options
		if (isset($options['security']))
		{
			$this->security = explode(',', $options['security']);
		}

		if (isset($options['force_ssl']))
		{
			$this->force_ssl = (bool) $options['force_ssl'];
		}

		if (isset($options['cookie_domain']))
		{
			$this->cookie_domain = (string) $options['cookie_domain'];
		}

		if (isset($options['cookie_path']))
		{
			$this->cookie_path = (string) $options['cookie_path'];
		}

		// Sync the session maxlifetime
		ini_set('session.gc_maxlifetime', $this->expire);

		return true;
	}

	/**
	 * Do some checks for security reason
	 *
	 * - timeout check (expire)
	 * - ip-fixiation
	 * - browser-fixiation
	 *
	 * If one check failed, session data has to be cleaned.
	 *
	 * @param   boolean  $restart  Reactivate session
	 * @return  boolean  True on success
	 * @see     http://shiflett.org/articles/the-truth-about-sessions
	 */
	protected function validate($restart = false)
	{
		// Allow to restart a session
		if ($restart)
		{
			$this->state = 'active';

			$this->set('session.client.address', null);
			$this->set('session.client.forwarded', null);
			$this->set('session.client.browser', null);
			$this->set('session.token', null);
		}

		// Check if session has expired
		if ($this->expire)
		{
			$curTime = $this->get('session.timer.now', 0);
			$maxTime = $this->get('session.timer.last', 0) + $this->expire;

			// Empty session variables
			if ($maxTime < $curTime)
			{
				$this->state = 'expired';
				return false;
			}
		}

		// Check for client address
		if (in_array('fix_adress', $this->security) && isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) !== false)
		{
			$ip = $this->get('session.client.address');

			if ($ip === null)
			{
				$this->set('session.client.address', $_SERVER['REMOTE_ADDR']);
			}
			elseif ($_SERVER['REMOTE_ADDR'] !== $ip)
			{
				$this->state = 'error';
				return false;
			}
		}

		// Record proxy forwarded for in the session in case we need it later
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP) !== false)
		{
			$this->set('session.client.forwarded', $_SERVER['HTTP_X_FORWARDED_FOR']);
		}

		return true;
	}
}
