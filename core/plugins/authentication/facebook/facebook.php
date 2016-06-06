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

class plgAuthenticationFacebook extends \Hubzero\Plugin\OauthClient
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
		// This is handled by the JS API, and cannot be done server side
		// (at least, it cannot be done server side, given our authentication workflow
		// and the current limitations of the PHP SDK).
	}

	/**
	 * Check login status of current user with regards to facebook
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		// Get the hub url
		$service    = trim(Request::base(), '/');
		$channelUrl = $service . '/channel.phtml';

		// This can only currently be done using the Facebook JS API
		// (at least relying solely on the native methods provided by the language's specific API)
		$js = "$(document).ready(function () {
			$('body').append('<div id=\"fb-root\"></div>');
			$.ajaxSetup({ cache: true });
			$.getScript('//connect.facebook.net/en_US/all.js', function () {
				window.fbAsyncInit = function () {
					FB.init({
						appId: '{$this->params->get('app_id')}',
						channelUrl: '{$channelUrl}'
					});

					FB.getLoginStatus(function ( response ) {
						if (response.status === 'connected') {
							FB.api('/me', function ( response ) {
								var facebook = $('#facebook').siblings('.sign-out');
								facebook.find('.current-user').html(response.name);

								facebook.on('click', function( e ) {
									e.preventDefault();
									FB.logout(function() {
										facebook.animate({'margin-top': -42}, function() {
											facebook.find('.current-user').html('');
										});
									});
								});
							});
						}
					});
				};
			});
		});";

		Document::addScriptDeclaration($js);
	}

	/**
	 * Method to call when redirected back from facebook after authentication
	 * Grab the return URL if set and handle denial of app privileges from facebook
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		$return = '';
		$b64dreturn = '';
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
		if (Request::getVar('error', NULL))
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				Lang::txt('PLG_AUTHENTICATION_FACEBOOK_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
				'error'
			);
		}
	}

	/**
	 * Method to setup facebook params and redirect to facebook auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		// Set up the config for the facebook sdk instance
		$config = array(
			'appId'      => $this->params->get('app_id'),
			'secret'     => $this->params->get('app_secret'),
			'fileUpload' => false
		);

		// Set up params for the login call
		$params = array(
			'scope'        => 'public_profile,email',
			'display'      => 'page',
			'redirect_uri' => self::getReturnUrl($view->return)
		);

		\Facebook\FacebookSession::setDefaultApplication($config['appId'], $config['secret']);

		$helper = new \Facebook\FacebookRedirectLoginHelper($params['redirect_uri']);
		$loginUrl = $helper->getLoginUrl(explode(',', $params['scope']));

		// Redirect to the login URL
		App::redirect($loginUrl);
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
		// Set up the config for the sdk instance
		$config = array(
			'appId'  => $this->params->get('app_id'),
			'secret' => $this->params->get('app_secret')
		);

		// Set defaults
		\Facebook\FacebookSession::setDefaultApplication($config['appId'], $config['secret']);

		$helper = new \Facebook\FacebookRedirectLoginHelper(self::getReturnUrl($options['return'], true));

		try
		{
			$session = $helper->getSessionFromRedirect();
		}
		catch (\Facebook\FacebookRequestException $ex)
		{
			// When Facebook returns an error
		}
		catch (\Exception $ex)
		{
			// When validation fails or other local issues
		}

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($session) && $session))
		{
			try
			{
				$request = new \Facebook\FacebookRequest($session, 'GET', '/me');
				$user_profile = $request->execute()->getGraphObject(\Facebook\GraphUser::className());

				$id       = $user_profile->getId();
				$fullname = $user_profile->getName();
				$email    = $user_profile->getProperty('email');
				$username = $user_profile->getProperty('username');
			}
			catch (\Facebook\FacebookRequestException $e)
			{
				// Error message?
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ERROR_RETRIEVING_PROFILE', $e->getMessage());
				return;
			}

			// Create the hubzero auth link
			$method = (Component::params('com_users')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'facebook', null, $id);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_UNKNOWN_USER');
				return;
			}

			$hzal->email = $email;

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'facebook';
			$response->status    = \Hubzero\Auth\Status::SUCCESS;
			$response->fullname  = $fullname;

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
				$sub_email    = explode('@', $email, 2);
				$tmp_username = (!empty($username)) ? $username : $sub_email[0];
				App::get('session')->set('auth_link.tmp_username', $tmp_username);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => 'https://graph.facebook.com/v2.0/' . $id . '/picture?type=normal',
					'authenticator' => 'facebook'
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_AUTHENTICATION_FAILED');
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
		// Set up the config for the sdk instance
		$config = array(
			'appId'  => $this->params->get('app_id'),
			'secret' => $this->params->get('app_secret')
		);

		// Set defaults
		\Facebook\FacebookSession::setDefaultApplication($config['appId'], $config['secret']);

		$helper = new \Facebook\FacebookRedirectLoginHelper(self::getReturnUrl($options['return']));

		try
		{
			$session = $helper->getSessionFromRedirect();
		}
		catch (\Facebook\FacebookRequestException $ex)
		{
			// When Facebook returns an error
		}
		catch (\Exception $ex)
		{
			// When validation fails or other local issues
		}

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($session) && $session))
		{
			try
			{
				$request = new \Facebook\FacebookRequest($session, 'GET', '/me');
				$user_profile = $request->execute()->getGraphObject(\Facebook\GraphUser::className());

				$id    = $user_profile->getId();
				$email = $user_profile->getProperty('email');
			}
			catch (\Facebook\FacebookRequestException $e)
			{
				// Error message?
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ERROR_RETRIEVING_PROFILE', $e->getMessage());
				return;
			}

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'facebook', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $id))
			{
				// This facebook account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'facebook', null, $id);
				$hzal->user_id = User::get('id');
				$hzal->email   = $email;
				$hzal->update();
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_FACEBOOK_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
				'error'
			);
		}
	}

	/**
	 * Generate return url
	 *
	 * @param   string  $return  url
	 * @param   bool    $encode  whether or not to encode return before using
	 * @return  string  url
	 */
	private static function getReturnUrl($return=null, $encode=false)
	{
		// Get the hub url
		$service = trim(Request::base(), '/');

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		// Check if a return is specified
		$rtrn = '';
		if (isset($return) && !empty($return))
		{
			if ($encode)
			{
				$return = base64_encode($return);
			}
			$rtrn = '&return=' . $return;
		}

		return self::getRedirectUri('facebook') . $rtrn;
	}
}