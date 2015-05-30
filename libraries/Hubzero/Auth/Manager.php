<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Auth;

use Hubzero\Container\Container;

/**
 * Authentication manager
 */
class Manager
{
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
	}

	/**
	 * Login authentication function.
	 *
	 * Username and encoded password are passed the onUserLogin event which
	 * is responsible for the user validation. A successful validation updates
	 * the current session record with the user's details.
	 *
	 * Username and encoded password are sent as credentials (along with other
	 * possibilities) to each observer (authentication plugin) for user
	 * validation.  Successful validation will update the current session with
	 * the user details.
	 *
	 * @param   array    $credentials  Array('username' => string, 'password' => string)
	 * @param   array    $options      Array('remember' => boolean)
	 * @return  boolean  True on success.
	 */
	public function login($credentials, $options = array())
	{
		$guard = new Guard($this->app);

		$response = $guard->authenticate($credentials, $options);

		if ($response->status === Status::SUCCESS)
		{
			// validate that the user should be able to login (different to being authenticated)
			// this permits authentication plugins blocking the user
			$authorisations = $guard->authorise($response, $options);

			$denied_states = array(
				Status::EXPIRED,
				Status::DENIED
			);

			foreach ($authorisations as $authorisation)
			{
				if (in_array($authorisation->status, $denied_states))
				{
					// Trigger onUserAuthorisationFailure Event.
					$this->app['dispatcher']->trigger('user.onUserAuthorisationFailure', array((array) $authorisation));

					// If silent is set, just return false.
					if (isset($options['silent']) && $options['silent'])
					{
						return false;
					}

					// Return the error.
					switch ($authorisation->status)
					{
						case Status::EXPIRED:
							return new Exception($this->app['language']->txt('JLIB_LOGIN_EXPIRED'), 102002, E_WARNING);
							break;
						case Status::DENIED:
							return new Exception($this->app['language']->txt('JLIB_LOGIN_DENIED'), 102003, E_WARNING);
							break;
						default:
							return new Exception($this->app['language']->txt('JLIB_LOGIN_AUTHORISATION'), 102004, E_WARNING);
							break;
					}
				}
			}

			// OK, the credentials are authenticated and user is authorised.  Lets fire the onLogin event.
			$results = $this->app['dispatcher']->trigger('user.onUserLogin', array((array) $response, $options));

			// If any of the user plugins did not successfully complete the login routine
			// then the whole method fails.
			//
			// Any errors raised should be done in the plugin as this provides the ability
			// to provide much more information about why the routine may have failed.
			if (!in_array(false, $results, true))
			{
				// Set the remember me cookie if enabled.
				if (isset($options['remember']) && $options['remember'])
				{
					// Create the encryption key, apply extra hardening using the user agent string.
					$privateKey = $this->app->hash(@$_SERVER['HTTP_USER_AGENT']);

					$key   = new \JCryptKey('simple', $privateKey, $privateKey);
					$crypt = new \JCrypt(new \JCryptCipherSimple, $key);
					$rcookie = $crypt->encrypt(json_encode($credentials));
					$lifetime = time() + 365 * 24 * 60 * 60;

					// Use domain and path set in config for cookie if it exists.
					$cookie_domain = $this->app['config']->get('cookie_domain', '');
					$cookie_path   = $this->app['config']->get('cookie_path', '/');

					// Check for SSL connection
					$secure = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION'));

					setcookie($this->app->hash('JLOGIN_REMEMBER'), $rcookie, $lifetime, $cookie_path, $cookie_domain, $secure, true);
				}

				return true;
			}
		}

		// Trigger onUserLoginFailure Event.
		$this->app['dispatcher']->trigger('user.onUserLoginFailure', array((array) $response));

		// If silent is set, just return false.
		if (isset($options['silent']) && $options['silent'])
		{
			return false;
		}

		// If status is success, any error will have been raised by the user plugin
		if ($response->status !== Status::SUCCESS)
		{
			return new Exception($response->error_message, 102001, E_WARNING);
		}

		return false;
	}

	/**
	 * Set the default authentication driver name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function logout($userid = null, $options = array())
	{
		// Get a user object
		$user = ($userid ? \User::getInstance($userid) : \User::getRoot());

		// Build the credentials array.
		$parameters['username'] = $user->get('username');
		$parameters['id'] = $user->get('id');

		// Set clientid in the options array if it hasn't been set already.
		if (!isset($options['clientid']))
		{
			$options['clientid'] = $this->app['client']->id;
		}

		// OK, the credentials are built. Lets fire the onLogout event.
		$results = $this->app['dispatcher']->trigger('user.onUserLogout', array($parameters, $options));

		// Check if any of the plugins failed. If none did, success.
		if (!in_array(false, $results, true))
		{
			// Use domain and path set in config for cookie if it exists.
			$cookie_domain = $this->app['config']->get('cookie_domain', '');
			$cookie_path   = $this->app['config']->get('cookie_path', '/');

			setcookie($this->app->hash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);

			return true;
		}

		// Trigger onUserLoginFailure Event.
		$this->app['dispatcher']->trigger('user.onUserLogoutFailure', array($parameters));

		return false;
	}
}
