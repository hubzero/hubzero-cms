<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Auth;

use Hubzero\Base\Object;
use Hubzero\Container\Container;

/**
 * Authentication class, provides an interface for the authentication system
 */
class Guard extends Object
{
	/**
	 * An array of Observer objects to notify
	 *
	 * @var  array
	 */
	protected $_observers = array();

	/**
	 * The state of the observable object
	 *
	 * @var  mixed
	 */
	protected $_state = null;

	/**
	 * A multi dimensional array of [function][] = key for observers
	 *
	 * @var  array
	 */
	protected $_methods = array();

	/**
	 * The application implementation.
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * Constructor
	 *
	 * @param   object  $app
	 * @return  void
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;

		$isLoaded = $this->app['plugin']->import('authentication');

		if (!$isLoaded)
		{
			$this->app['log.debug']->error($this->app['language']->txt('JLIB_USER_ERROR_AUTHENTICATION_LIBRARIES'));
		}
	}

	/**
	 * Get the state of the object
	 *
	 * @return  mixed  The state of the object.
	 */
	public function getState()
	{
		return $this->_state;
	}

	/**
	 * Attach an observer object
	 *
	 * [!] Based on Joomla's event dispatcher
	 *     This is here purely for compatibility.
	 *
	 * @param   object  $observer  An observer object to attach
	 * @return  void
	 * @todo    Update plugins to not need this and remove method
	 */
	public function attach($observer)
	{
		if (is_array($observer))
		{
			if (!isset($observer['handler']) || !isset($observer['event']) || !is_callable($observer['handler']))
			{
				return;
			}

			// Make sure we haven't already attached this array as an observer
			foreach ($this->_observers as $check)
			{
				if (is_array($check) && $check['event'] == $observer['event'] && $check['handler'] == $observer['handler'])
				{
					return;
				}
			}

			$this->_observers[] = $observer;
			end($this->_observers);
			$methods = array($observer['event']);
		}
		else
		{
			if (!($observer instanceof Guard))
			{
				return;
			}

			// Make sure we haven't already attached this object as an observer
			$class = get_class($observer);

			foreach ($this->_observers as $check)
			{
				if ($check instanceof $class)
				{
					return;
				}
			}

			$this->_observers[] = $observer;
			$methods = array_diff(get_class_methods($observer), get_class_methods('Hubzero\\Plugin\\Plugin'));
		}

		$key = key($this->_observers);

		foreach ($methods as $method)
		{
			$method = strtolower($method);

			if (!isset($this->_methods[$method]))
			{
				$this->_methods[$method] = array();
			}

			$this->_methods[$method][] = $key;
		}
	}

	/**
	 * Detach an observer object
	 *
	 * [!] Based on Joomla's event dispatcher
	 *     This is here purely for compatibility.
	 *
	 * @param   object   $observer  An observer object to detach.
	 * @return  boolean  True if the observer object was detached.
	 * @todo    Update plugins to not need this and remove method
	 */
	public function detach($observer)
	{
		// Initialise variables.
		$retval = false;

		$key = array_search($observer, $this->_observers);

		if ($key !== false)
		{
			unset($this->_observers[$key]);
			$retval = true;

			foreach ($this->_methods as &$method)
			{
				$k = array_search($key, $method);

				if ($k !== false)
				{
					unset($method[$k]);
				}
			}
		}

		return $retval;
	}

	/**
	 * Finds out if a set of login credentials are valid by asking all observing
	 * objects to run their respective authentication routines.
	 *
	 * @param   array   $credentials  Array holding the user credentials.
	 * @param   array   $options      Array holding user options.
	 * @return  object  Response object with status variable filled in for last plugin or first successful plugin.
	 */
	public function authenticate($credentials, $options = array())
	{
		// Get plugins
		$plugins = $this->app['plugin']->byType('authentication');

		// Create authentication response
		$response = new Response;

		// Loop through the plugins and check of the credentials can be used to authenticate
		// the user
		//
		// Any errors raised in the plugin should be returned via the JAuthenticationResponse
		// and handled appropriately.
		foreach ($plugins as $plugin)
		{
			if (!empty($options['authenticator']) && ($plugin->name != $options['authenticator']))
			{
				continue;
			}

			$className = 'plg' . $plugin->type . $plugin->name;
			if (class_exists($className))
			{
				$plugin = new $className($this, (array) $plugin);
			}
			else
			{
				// Bail here if the plugin can't be created
				$this->app['log.debug']->error($this->app['language']->txts('JLIB_USER_ERROR_AUTHENTICATION_FAILED_LOAD_PLUGIN', $className));
				continue;
			}

			// If backend login, make sure plugin is enabled for backend
			if ($options['action'] == 'core.login.admin' && !$plugin->params->get('admin_login', false))
			{
				continue;
			}

			// Try to authenticate
			$plugin->onUserAuthenticate($credentials, $options, $response);

			// If authentication is successful break out of the loop
			if ($response->status === Status::SUCCESS)
			{
				if (empty($response->type))
				{
					$response->type = isset($plugin->_name) ? $plugin->_name : $plugin->name;
				}
				break;
			}
		}

		if (empty($response->username))
		{
			$response->username = $credentials['username'];
		}

		if (empty($response->fullname))
		{
			$response->fullname = $credentials['username'];
		}

		if (empty($response->password))
		{
			$response->password = $credentials['password'];
		}

		return $response;
	}

	/**
	 * Authorises that a particular user should be able to login
	 *
	 * @param   object  $response  response including username of the user to authorise
	 * @param   array   $options   list of options
	 * @return  array   Results of authorisation
	 */
	public function authorise($response, $options = array())
	{
		// Get plugins in case they haven't been loaded already
		$this->app['plugin']->byType('user');
		$this->app['plugin']->byType('authentication');

		return $this->app['dispatcher']->trigger('onUserAuthorisation', array($response, $options));
	}
}