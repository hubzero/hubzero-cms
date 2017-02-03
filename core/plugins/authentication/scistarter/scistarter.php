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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use SciStarter\Oauth;

require_once __DIR__ . '/SciStarter/Http/Curl.php';
require_once __DIR__ . '/SciStarter/Oauth.php';

class plgAuthenticationSciStarter extends \Hubzero\Plugin\OauthClient
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Perform logout (not currently used)
	 *
	 * @return  void
	 */
	public function logout()
	{
		// @TODO: implement me
	}

	/**
	 * Check login status of current user with regards to scistarter
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		// @TODO: implement me
	}

	/**
	 * Method to call when redirected back from scistarter after authentication
	 * Grab the return URL if set and handle denial of app privileges from scistarter
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		// Set up the config for the api instance
		$client = new Oauth();
		if ($this->params->get('environment') == 'sandbox')
		{
			$client->useSandboxEnvironment();
		}
		$client->setClientId($this->params->get('app_id'))
		       ->setClientSecret($this->params->get('app_secret'))
		       ->setRedirectUri(self::getRedirectUri('scistarter'));

		// If we have a code coming back, the user has authorized our app, and we can authenticate
		if ($code = Request::getVar('code', NULL))
		{
			// Authenticate the user
			$client->authenticate($code);

			// Add the access token to the session
			Session::set('scistarter.token', $client->getAccessToken());
		}
		else
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode('/members/myaccount')),
				Lang::txt('PLG_AUTHENTICATION_SCISTARTER_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
				'error'
			);
		}
	}

	/**
	 * Method to setup scistarter params and redirect to scistarter auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		// Set up the config for the SciStarter api instance
		$client = new Oauth();
		if ($this->params->get('environment') == 'sandbox')
		{
			$client->useSandboxEnvironment();
		}
		$client->setClientId($this->params->get('app_id'))
		       ->setRedirectUri(self::getRedirectUri('scistarter'));

		// Redirect to the login URL
		App::redirect($client->getAuthorizationUrl());
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
		// Set up the config for the api instance
		$client = new Oauth();
		if ($this->params->get('environment') == 'sandbox')
		{
			$client->useSandboxEnvironment();
		}
		$client->setClientId($this->params->get('app_id'))
		       ->setClientSecret($this->params->get('app_secret'))
		       ->setRedirectUri(self::getRedirectUri('scistarter'));

		if (App::get('session')->get('scistarter.token', NULL))
		{
			$client->setAccessToken(App::get('session')->get('scistarter.token'));
		}

		// If we have an access token set, carry on
		if ($client->isAuthenticated())
		{
			$account = $client->getUserData();

			$accountIsOk = true;
			if(!isset($account->user_id) || $account->user_id <= 0) {
				$accountIsOk = false;
				$error_message = $response->error_message = Lang::txt('PLG_AUTHENTICATION_SCISTARTER_AUTHENTICATION_FAILED_NO_UID');
			}
			elseif(!isset($account->email) || !$account->email) {
				$accountIsOk = false;
				$error_message = $response->error_message = Lang::txt('PLG_AUTHENTICATION_SCISTARTER_AUTHENTICATION_FAILED_NO_EMAIL');
			}

			// Make sure we have a Scistarter account
			if ($accountIsOk)
			{
				$username = (string) $account->email;

				// Create the hubzero auth link
				$method = (Component::params('com_users')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
				$hzal = \Hubzero\Auth\Link::$method('authentication', 'scistarter', null, $username);

				if ($hzal === false)
				{
					$response->status = \Hubzero\Auth\Status::FAILURE;
					$response->error_message = Lang::txt('PLG_AUTHENTICATION_SCISTARTER_UNKNOWN_USER');
					return;
				}

				$hzal->email = $account->email;

				// Set response variables
				$response->auth_link = $hzal;
				$response->type      = 'scistarter';
				$response->status    = \Hubzero\Auth\Status::SUCCESS;
				$response->fullname  = $account->email;

				if (!empty($hzal->user_id))
				{
					$user = User::getInstance($hzal->user_id);

					$response->username = $user->username;
					$response->email    = $user->email;
					$response->fullname = $user->name;
				}
				else
				{
					$response->username = '-' . $hzal->id;
					$response->email    = $response->username . '@invalid';

					// Also set a suggested username for their hub account
					$sub_email    = explode('@', $account->email, 2);
					$tmp_username = $sub_email[0];

					Session::set('auth_link.tmp_username', $tmp_username);
				}

				$hzal->update();

				// If we have a real user, drop the authenticator cookie
				if (isset($user) && is_object($user))
				{
					// Set cookie with login preference info
					$prefs = array(
						'user_id'       => $user->get('id'),
						'user_img'      => $user->picture(0, false),
						'authenticator' => 'scistarter'
					);

					$namespace = 'authenticator';
					$lifetime  = time() + 365*24*60*60;

					\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
				}
			}
			else
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = $error_message;
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_SCISTARTER_AUTHENTICATION_FAILED');
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
		// Set up the config for the api instance
		$client = new Oauth();
		if ($this->params->get('environment') == 'sandbox')
		{
			$client->useSandboxEnvironment();
		}
		$client->setClientId($this->params->get('app_id'))
		       ->setClientSecret($this->params->get('app_secret'))
		       ->setRedirectUri(self::getRedirectUri('scistarter'));

		// If we have a code coming back, the user has authorized our app, and we can authenticate
		if ($code = Request::getVar('code', NULL))
		{
			// Authenticate the user
			$client->authenticate($code);
		}
		else
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode('/members/myaccount')),
				Lang::txt('PLG_AUTHENTICATION_SCISTARTER_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
				'error'
			);
		}

		if ($client->isAuthenticated())
		{
			$account = $client->getUserData();
		}
		else
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_SCISTARTER_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
				'error'
			);
		}

		// Make sure we have a scistarter account
		if ($account->user_id > 0)
		{
			$username = (string) $account->email;

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'scistarter', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This scistarter account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_SCISTARTER_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'scistarter', null, $username);
				$hzal->user_id = User::get('id');
				$hzal->update();
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_SCISTARTER_AUTHENTICATION_FAILED', Config::get('sitename')),
				'error'
			);
		}
	}
}