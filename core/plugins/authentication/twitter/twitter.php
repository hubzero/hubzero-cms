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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Include php library
require_once PATH_CORE . DS . 'libraries' . DS . 'twitteroauth' . DS . 'twitteroauth.php';

class plgAuthenticationTwitter extends \Hubzero\Plugin\OauthClient
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
	 * Check login status of current user with regards to twitter
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		// @TODO: implement me
	}

	/**
	 * Method to call when redirected back from twitter after authentication
	 * Grab the return URL if set and handle denial of app privileges from twitter
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		if ($return = Request::getVar('return', '', 'method', 'base64'))
		{
			$b64dreturn = base64_decode($return);
			if (!\Hubzero\Utility\Uri::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;

		// Check to make sure they didn't deny our application permissions
		if (Request::getWord('denied', false))
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				Lang::txt('PLG_AUTHENTICATION_TWITTER_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
				'error'
			);
			return;
		}
	}

	/**
	 * Method to setup twitter params and redirect to twitter auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		// Check if a return is specified
		if ($view->return)
		{
			$return = '&return=' . $view->return;
		}

		// Build twitter object
		$twitter = new TwitterOAuth($this->params->get('app_id'), $this->params->get('app_secret'));

		// Set callback url and get temp credentials
		$temporary_credentials = $twitter->getRequestToken(self::getRedirectUri('twitter') . $return);

		// Store temp credentials in session for use after authentication redirect from twitter
		App::get('session')->set('twitter.oauth.token', $temporary_credentials['oauth_token']);
		App::get('session')->set('twitter.oauth.token_secret', $temporary_credentials['oauth_token_secret']);

		// Get login url
		$redirect_url = $twitter->getAuthorizeURL($temporary_credentials);

		// Redirect to the login URL
		App::redirect($redirect_url);
		return;
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
		// Build twitter object using temp credentials saved in session
		$twitter = new TwitterOAuth(
			$this->params->get('app_id'),
			$this->params->get('app_secret'),
			App::get('session')->get('twitter.oauth.token'),
			App::get('session')->get('twitter.oauth.token_secret')
		);

		// Request user specific (longer lasting) credentials
		$token_credentials = $twitter->getAccessToken(Request::getVar('oauth_verifier'));

		// Build new twitter object with user credentials
		$twitter = new TwitterOAuth(
			$this->params->get('app_id'),
			$this->params->get('app_secret'),
			$token_credentials['oauth_token'],
			$token_credentials['oauth_token_secret']
		);

		// Get user account info
		$account = $twitter->get('account/verify_credentials');

		// Make sure we have a twitter account
		if (!$account->errors && $account->id > 0)
		{
			// Get id as username (silly, but we cast to string, otherwise find_or_create bellow fails)
			$username = (string) $account->id;

			// Create the hubzero auth link
			$method = (Component::params('com_users')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'twitter', null, $username);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_TWITTER_UNKNOWN_USER');
				return;
			}

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'twitter';
			$response->status    = \Hubzero\Auth\Status::SUCCESS;
			$response->fullname  = $account->name;

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
				App::get('session')->set('auth_link.tmp_username', $account->screen_name);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => str_replace('_normal', '', $account->profile_image_url_https),
					'authenticator' => 'twitter'
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_TWITTER_AUTHENTICATION_FAILED');
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
		// Build twitter object using temp credentials saved in session
		$twitter = new TwitterOAuth(
			$this->params->get('app_id'),
			$this->params->get('app_secret'),
			App::get('session')->get('twitter.oauth.token'),
			App::get('session')->get('twitter.oauth.token_secret')
		);

		// Request user specific (longer lasting) credentials
		$token_credentials = $twitter->getAccessToken(Request::getVar('oauth_verifier'));

		// Build new twitter object with user credentials
		$twitter = new TwitterOAuth(
			$this->params->get('app_id'),
			$this->params->get('app_secret'),
			$token_credentials['oauth_token'],
			$token_credentials['oauth_token_secret']
		);

		// Get user account info
		$account = $twitter->get('account/verify_credentials');

		// Make sure we have a twitter account
		if (!$account->errors && $account->id > 0)
		{
			// Get unique username
			$username = (string) $account->id;

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'twitter', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This twitter account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_TWITTER_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
				return;
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'twitter', null, $username);
				$hzal->user_id = User::get('id');
				$hzal->update();
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_TWITTER_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
				'error'
			);
			return;
		}
	}
}