<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class plgAuthenticationGoogle extends \Hubzero\Plugin\OauthClient
{
	/**
	 * Perform logout (handled via JS)
	 *
	 * @return  void
	 */
	public function logout()
	{
		// This is handled by the JS API, and cannot be done server side
		// (at least, it cannot be done server side, given our authentication workflow
		// and the current limitations of the PHP SDK).
	}

	/**
	 * Check login status of current user with regards to google
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		// Essentually, in the background, we're just going to try and make a request to check if the user is logged in
		// If they are, we'll add their email address to the google login button, thus offering the sign out option as well
		// @FIXME: logout below is a total hack!
		$js = "(function() {
					var po   = document.createElement('script');
					po.type  = 'text/javascript';
					po.async = true;
					po.src   = 'https://apis.google.com/js/client:plusone.js?onload=OnLoadCallback';
					var s    = document.getElementsByTagName('script')[0];
					s.parentNode.insertBefore(po, s);

					OnLoadCallback = function () {
						gapi.auth.authorize({
							client_id     : '{$this->params->get('app_id')}',
							immediate     : true,
							response_type : 'token',
							scope         : 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
						}, function ( response ) {
							if (response && !response.error) {
								gapi.client.load('oauth2', 'v2', function () {
									var request = gapi.client.oauth2.userinfo.get({
										'userId' : 'me'
									});
									request.execute(function ( resp ) {
										var google = $('#google').siblings('.sign-out');
										google.find('.current-user').html(resp.email);

										google.on('click', function ( e ) {
											e.preventDefault();
											$('body').append('<iframe src=\"https://accounts.google.com/logout\" style=\"display:none;\">');
											google.animate({'margin-top': -42}, function() {
												google.find('.current-user').html('');
											});
										});
									});
								});
							}
						});
					}
				})();";

		Document::addScriptDeclaration($js);
	}

	/**
	 * Method to call when redirected back from google after authentication
	 * Grab the return URL if set and handle denial of app privileges from google
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		$b64dreturn = '';

		// Check the state for our return variable
		if ($return = Request::getVar('state', '', 'method', 'base64'))
		{
			$b64dreturn = base64_decode($return);
			if (!JURI::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;

		// Set up the config for the google api instance
		$client = new Google_Client();
		$client->setClientId($this->params->get('app_id'));
		$client->setClientSecret($this->params->get('app_secret'));
		$client->setRedirectUri(self::getRedirectUri('google'));

		// If we have a code comeing back, the user has authorized our app, and we can authenticate
		if ($code = Request::getVar('code', NULL))
		{
			// Authenticate the user
			$client->authenticate($code);

			// Add the access token to the session
			$jsession = App::get('session');
			$jsession->set('google.token', $client->getAccessToken());
		}
		else
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				'To log in via Google, you must authorize the ' . Config::get('sitename') . ' app.',
				'error'
			);
		}
	}

	/**
	 * Method to setup google params and redirect to google auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		// Set up the config for the google api instance
		$client = new Google_Client();
		$client->setClientId($this->params->get('app_id'));
		$client->setClientSecret($this->params->get('app_secret'));
		$client->setRedirectUri(self::getRedirectUri('google'));
		$client->setAccessType('online');
		$client->setState($view->return);
		$client->setApprovalPrompt('auto');
		$client->setScopes('https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');

		// Create and follow the authorization URL
		App::redirect($client->createAuthUrl());
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
		// Set up the config for the google api instance
		$client = new Google_Client();
		$client->setClientId($this->params->get('app_id'));
		$client->setClientSecret($this->params->get('app_secret'));

		// Create OAuth2 Instance
		$oauth2 = new Google_Service_OAuth2($client);

		// Check if there's an active token in the session
		$jsession = App::get('session');
		if ($jsession->get('google.token', NULL))
		{
			$client->setAccessToken($jsession->get('google.token'));
		}

		// If we have an access token set, carry on
		if ($client->getAccessToken())
		{
			// Get the user info
			$user_profile = $oauth2->userinfo->get();

			// Set username to email address, which will fail and force registration update
			// (we have to make sure we get something unique from google)
			$username = $user_profile['email'];

			// Create the hubzero auth link
			$method = (Component::params('com_users')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'google', null, $username);

			if ($hzal === false)
			{
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Unknown user and new user registration is not permitted.';
				return;
			}

			$hzal->email = $user_profile['email'];

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'google';
			$response->status    = JAUTHENTICATE_STATUS_SUCCESS;
			$response->fullname  = $user_profile['name'];

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
				$sub_email    = explode('@', $user_profile['email'], 2);
				$tmp_username = $sub_email[0];
				$jsession->set('auth_link.tmp_username', $tmp_username);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => $user_profile['picture'] . '?sz=100',
					'authenticator' => 'google'
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Username and password do not match or you do not have an account yet.';
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
		// Set up the config for the google api instance
		$client = new Google_Client();
		$client->setClientId($this->params->get('app_id'));
		$client->setClientSecret($this->params->get('app_secret'));
		$client->setRedirectUri(self::getRedirectUri('google'));

		// Create OAuth2 Instance
		$oauth2 = new Google_Service_OAuth2($client);

		// If we have this code, we know we have a successful return from google
		if ($code = Request::getVar('code', NULL))
		{
			// Authenticate the user
			$client->authenticate($code);
		}

		// If we have an access token set, carry on
		if ($client->getAccessToken())
		{
			// Get the user info
			$user_profile = $oauth2->userinfo->get();

			// Make sure we use something unique and consistent here!
			$username = $user_profile['email'];

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'google', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This google account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					'This google account appears to already be linked to a hub account',
					'error'
				);
			}
			else
			{
				// Create the hubzero auth link
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'google', null, $username);
				$hzal->user_id = User::get('id');
				$hzal->email   = $user_profile['email'];
				$hzal->update();
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel...
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				'To log in via Google, you must authorize the ' . Config::get('sitename') . ' app.',
				'error'
			);
		}
	}
}