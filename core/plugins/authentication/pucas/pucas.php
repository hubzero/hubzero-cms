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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once PATH_CORE . DS . 'libraries' . DS . 'CAS-1.3.3' . DS . 'CAS.php';

/**
 * Authentication Plugin class for PUCAS
 */
class plgAuthenticationPUCAS extends \Hubzero\Plugin\OauthClient
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Actions to perform when logging out a user session
	 *
	 * @return  void
	 */
	public function logout()
	{
		if (Config::get('debug'))
		{
			$debug_location = $this->params->get('debug_location', '/var/log/apache2/php/phpCAS.log');
			phpCAS::setDebug($debug_location);
		}

		$this->initialize();

		$service = rtrim(Request::base(),'/');

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		$return = '';

		if ($return = Request::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);

			if (!\Hubzero\Utility\Uri::isInternal($return))
			{
				$return = '';
			}

			$return = '/' . ltrim($return, '/');
		}

		phpCAS::logout(array('service'=>$service . $return, 'url'=>$service . $return));
	}

	/**
	 * Check login status of current user with regards to Purdue CAS
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		$status = array();

		if (Config::Get('debug'))
		{
			$debug_location = $this->params->get('debug_location', '/var/log/apache2/php/phpCAS.log');
			phpCAS::setDebug($debug_location);
		}

		$this->initialize();

		if (phpCAS::checkAuthentication())
		{
			$status['username'] = phpCAS::getUser();
		}
		return $status;
	}

	/**
	 * Actions to perform when logging in a user session
	 *
	 * @param   object  &$credentials
	 * @param   array   &$options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		if ($return = Request::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);
			if (!\Hubzero\Utility\Uri::isInternal($return))
			{
				$return = '';
			}
		}

		$options['return'] = $return;
	}

	/**
	 * Method to setup Purdue CAS params and redirect to pucas auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		if (Config::get('debug'))
		{
			$debug_location = $this->params->get('debug_location', '/var/log/apache2/php/phpCAS.log');
			phpCAS::setDebug($debug_location);
		}

		$this->initialize();

		$return = '';
		if ($view->return)
		{
			$return = '&return=' . $view->return;
		}

		if ($this->isBoilerkeyRequired())
		{
			$loginUrl  = 'https://www.purdue.edu/apps/account/cas/login?boilerkeyRequired=true&service=';
			$loginUrl .= urlencode(self::getRedirectUri('pucas') . $return);

			phpCAS::setServerLoginURL($loginUrl);
		}
		else
		{
			phpCAS::setFixedServiceURL(self::getRedirectUri('pucas') . $return);
		}

		phpCAS::forceAuthentication();

		App::redirect(self::getRedirectUri('pucas') . $return);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object   $response     Authentication response object
	 * @return  boolean
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		return $this->onUserAuthenticate($credentials, $options, $response);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object   $response     Authentication response object
	 * @return  boolean
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		if (Config::get('debug'))
		{
			$debug_location = $this->params->get('debug_location', '/var/log/apache2/php/phpCAS.log');
			phpCAS::setDebug($debug_location);
		}

		$this->initialize();

		try
		{
			$authenticated = phpCAS::isAuthenticated();
		}
		catch (CAS_AuthenticationException $e)
		{
			throw new Exception(Lang::txt('PLG_AUTHENTICATION_PUCAS_ERROR_EXPIRED_TICKET'), 400);
		}

		$return = (isset($options['return'])) ? $options['return'] : '';
		if ($authenticated && $this->checkBoilerkey($return))
		{
			$username = phpCAS::getUser();

			$method = (Component::params('com_users')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'pucas', null, $username);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_PUCAS_UNKNOWN_USER');
				return;
			}

			$hzal->email = $username . '@purdue.edu';

			$response->auth_link = $hzal;
			$response->type = 'pucas';
			$response->status = \Hubzero\Auth\Status::SUCCESS;

			$email = phpCAS::getAttribute('email');
			$name  = phpCAS::getAttribute('fullname');

			if (!empty($email))
			{
				$hzal->email = $email;
			}

			if (!empty($name))
			{
				$response->fullname = ucwords(strtolower($name));
			}

			if (!empty($hzal->user_id))
			{
				$user = User::getInstance($hzal->user_id); // Bring this in line with the rest of the system

				$response->username = $user->get('username');
				$response->email    = $user->get('email');
				$response->fullname = $user->get('name');
			}
			else
			{
				$response->username = '-' . $hzal->id; // The Open Group Base Specifications Issue 6, Section 3.426
				$response->email    = $response->username . '@invalid'; // RFC2606, section 2

				// Also set a suggested username for their hub account
				App::get('session')->set('auth_link.tmp_username', $username);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => $user->picture(0, false),
					'authenticator' => 'pucas'
				);
			}
			else
			{
				// A partially baked cookie when a new user account is created.
				$prefs = array(
					'authenticator' => 'pucas'
				);
			}

			$namespace = 'authenticator';
			$lifetime  = time() + 365*24*60*60;

			\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_PUCAS_AUTHENTICATION_FAILED');
		}
	}

	/**
	 * Similar to onAuthenticate, except we already have a logged in user, we're just linking accounts
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function link($options=array())
	{
		if (Config::get('debug'))
		{
			$debug_location = $this->params->get('debug_location', '/var/log/apache2/php/phpCAS.log');
			phpCAS::setDebug($debug_location);
		}

		$this->initialize();

		if (phpCAS::isAuthenticated() && $this->checkBoilerkey())
		{
			// Get unique username
			$username = phpCAS::getUser();

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'pucas', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This purdue cas account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_PUCAS_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'pucas', null, $username);
				$hzal->user_id = User::get('id');
				$hzal->email   = phpCAS::getAttribute('email');
				$hzal->update();
			}
		}
		else
		{
			// User somehow got redirect back without being authenticated (not sure how this would happen?)
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_PUCAS_ERROR_LINKING'),
				'error'
			);
		}
	}

	/**
	 * Initializes the PHP CAS client
	 *
	 * @return void
	 **/
	private function initialize()
	{
		if (!phpCAS::isInitialized())
		{
			phpCAS::client(CAS_VERSION_2_0, 'www.purdue.edu', 443, '/apps/account/cas', false);
		}

		phpCAS::setNoCasServerValidation();
	}

	/**
	 * Checks to see if boilerkey is required
	 *
	 * @return bool
	 **/
	private function isBoilerkeyRequired()
	{
		$boilerkeyRequired = $this->params->get('boilerkey_required', 'none');

		if ($boilerkeyRequired == 'both' || $boilerkeyRequired == App::get('client')->name)
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks to see if boilerkey is required, and if so, is present
	 *
	 * @param  string $return the return location
	 * @return bool
	 **/
	private function checkBoilerkey($return='')
	{
		// If boilerkey isn't required, just return true for our check
		if (!$this->isBoilerkeyRequired())
		{
			return true;
		}

		// Check the last auth time for boilerkey
		$lastAuth = phpCAS::getAttribute('boilerkeyauthtime');

		// If there is a last auth time, we just have to make sure it's not
		// above the configurable threshold
		if (isset($lastAuth) && !empty($lastAuth))
		{
			$current  = time();
			$lastAuth = strtotime($lastAuth);

			// Take the absolute value just in case system times are slightly out of sync
			$diff = abs($current - $lastAuth);

			if (($diff / 60) < $this->params->get('boilerkey_timeout', 15))
			{
				return true;
			}
		}

		// We either don't have a cas session with boilerkey, or it's too old.
		// So we essentially make them reauth.
		$return    = (!empty($return)) ? '&return=' . base64_encode($return) : '';
		$loginUrl  = 'https://www.purdue.edu/apps/account/cas/logout?reauthWithBoilerkeyService=';
		// Not sure why we need to encode twice.  I think somewhere along the lines, the CAS server
		// removes the encoding once.
		$loginUrl .= urlencode(urlencode(self::getRedirectUri('pucas') . $return));

		// Kill the session var holding the CAS ticket, otherwise it will find the old session
		// and never actually redirect to the CAS server logout/login page
		unset($_SESSION['phpCAS']);

		phpCAS::setServerLoginURL($loginUrl);
		phpCAS::forceAuthentication();
	}
}
