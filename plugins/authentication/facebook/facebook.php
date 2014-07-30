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

class plgAuthenticationFacebook extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 */
	function plgAuthenticationJoomla(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Perform logout (not currently used)
	 *
	 * @access	public
	 * @return	void
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
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		// Get the hub url
		$juri       = JURI::getInstance();
		$service    = trim($juri->base(), DS);
		$channelUrl = $service . DS . 'channel.phtml';

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

		JFactory::getDocument()->addScriptDeclaration($js);
	}

	/**
	 * Method to call when redirected back from facebook after authentication
	 * Grab the return URL if set and handle denial of app privileges from facebook
	 *
	 * @access	public
	 * @param   object	$credentials
	 * @param 	object	$options
	 * @return	void
	 */
	public function login(&$credentials, &$options)
	{
		$app = JFactory::getApplication();

		$return = '';
		$b64dreturn = '';
		if ($return = JRequest::getVar('return', '', 'method', 'base64'))
		{
			$b64dreturn = base64_decode($return);
			if (!JURI::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;

		// Check to make sure they didn't deny our application permissions
		if (JRequest::getVar('error', NULL))
		{
			// User didn't authorize our app or clicked cancel
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return=' . $return),
				'To log in via Facebook, you must authorize the ' . $app->getCfg('sitename') . ' app.',
				'error');
		}
	}

	/**
	 * Method to setup facebook params and redirect to facebook auth URL
	 *
	 * @access	public
	 * @param   object	$view	view object
	 * @param 	object	$tpl	template object
	 * @return	void
	 */
	public function display($view, $tpl)
	{
		$app = JFactory::getApplication();
		$ver = $this->params->get('api_version', 1.0);

		// Set up the config for the facebook sdk instance
		$config               = array();
		$config['appId']      = $this->params->get('app_id');
		$config['secret']     = $this->params->get('app_secret');
		$config['fileUpload'] = false;

		// Set up params for the login call
		$params = array(
			'scope'        => 'public_profile,email,user_birthday',
			'display'      => 'page',
			'redirect_uri' => self::getReturnUrl($view->return)
		);

		switch ($ver)
		{
			case 2.0:
				\Facebook\FacebookSession::setDefaultApplication($config['appId'], $config['secret']);

				$helper = new \Facebook\FacebookRedirectLoginHelper($params['redirect_uri']);
				$loginUrl = $helper->getLoginUrl(explode(',', $params['scope']));
				break;
			case 1.0:
			default:
				// Create facebook instance
				$facebook = new Facebook($config);

				// Get the login URL
				$loginUrl = $facebook->getLoginUrl($params);
				break;
		}

		// Redirect to the login URL
		$app->redirect($loginUrl);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	 Authentication response object
	 * @return	boolean
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		return $this->onUserAuthenticate($credentials, $options, $response);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array 	$credentials Array holding the user credentials
	 * @param 	array   $options     Array of extra options
	 * @param	object	$response	 Authentication response object
	 * @return	boolean
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		// Check which version of facebook api should be used
		$ver = $this->params->get('api_version', 1.0);

		// Set up the config for the sdk instance
		$config           = array();
		$config['appId']  = $this->params->get('app_id');
		$config['secret'] = $this->params->get('app_secret');

		switch ($ver)
		{
			case 2.0:
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
				break;
			case 1.0:
			default:
				// Create instance and get the facebook user_id
				$facebook = new Facebook($config);
				$user_id  = $facebook->getUser();
				break;
		}

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($session) && $session))
		{
			switch ($ver)
			{
				case 2.0:
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
						$response->status = JAUTHENTICATE_STATUS_FAILURE;
						$response->error_message = 'Failed to retrieve Facebook profile (' . $e->getMessage() . ').';
						return;
					}
					break;
				case 1.0:
				default:
					// Get the facebook graph api profile for the user
					$user_profile = $facebook->api('/me','GET');

					// Get unique username/id
					// We'll use facebook id - could also use facebook username, but not everyone has one defined
					$id       = $user_profile['id'];
					$fullname = $user_profile['name'];
					$email    = $user_profile['email'];
					$username = $user_profile['username'];
					break;
			}

			// Create the hubzero auth link
			$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'facebook', null, $id);
			$hzal->email = $email;

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'facebook';
			$response->status    = JAUTHENTICATE_STATUS_SUCCESS;
			$response->fullname  = $fullname;

			if (!empty($hzal->user_id))
			{
				$user = JUser::getInstance($hzal->user_id);

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
				JFactory::getSession()->set('auth_link.tmp_username', $tmp_username);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs                  = array();
				$prefs['user_id']       = $user->get('id');
				$prefs['user_img']      = 'https://graph.facebook.com/v2.0/'.$id.'/picture?type=normal';
				$prefs['authenticator'] = 'facebook';

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
	 * @access	public
	 * @param   array - $options
	 * @return	void
	 */
	public function link($options=array())
	{
		// Check which version of facebook api should be used
		$ver   = $this->params->get('api_version', 1.0);
		$app   = JFactory::getApplication();
		$juser = JFactory::getUser();

		// Set up the config for the sdk instance
		$config           = array();
		$config['appId']  = $this->params->get('app_id');
		$config['secret'] = $this->params->get('app_secret');

		switch ($ver)
		{
			case 2.0:
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
				break;
			case 1.0:
			default:
				// Create instance and get the facebook user_id
				$facebook = new Facebook($config);
				$user_id  = $facebook->getUser();
				break;
		}

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($session) && $session))
		{
			switch ($ver)
			{
				case 2.0:
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
						$response->status = JAUTHENTICATE_STATUS_FAILURE;
						$response->error_message = 'Failed to retrieve Facebook profile (' . $e->getMessage() . ').';
						return;
					}
					break;
				case 1.0:
				default:
					// Get the facebook graph api profile for the user
					$user_profile = $facebook->api('/me','GET');

					// Get unique username/id
					// We'll use facebook id - could also use facebook username, but not everyone has one defined
					$id    = $user_profile['id'];
					$email = $user_profile['email'];
					break;
			}

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'facebook', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $id))
			{
				// This facebook account is already linked to another hub account
				$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'),
					'This facebook account appears to already be linked to a hub account',
					'error');
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'facebook', null, $id);
				$hzal->user_id = $juser->get('id');
				$hzal->email   = $email;
				$hzal->update();
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel
			$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'),
				'To link the current account with your Facebook account, you must authorize the ' . $app->getCfg('sitename') . ' app.',
				'error');
		}
	}

	/**
	 * Get user profile info provided via facebook
	 *
	 * We pass params to avoid creating an instance of the plugin and carrying the joomla 'event' bagage with it
	 *
	 * @access	public
	 * @param	object	$params	 Plugin params
	 * @return	void
	 */
	public static function getInfo($params)
	{
		// Set up the config for the sdk instance
		$params = json_decode($params);

		$config           = array();
		$config['appId']  = $params->app_id;
		$config['secret'] = $params->app_secret;

		// Create instance and get the facebook user_id
		$facebook = new Facebook($config);
		$user_id  = $facebook->getUser();

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if ($user_id > 0)
		{
			// Get the facebook graph api profile for the user
			$user_profile = $facebook->api('/me','GET');

			return $user_profile;
		}
		else
		{
			// Not currently logged in
		}
	}

	/**
	 * Generate return url
	 *
	 * @param  (string) return url
	 * @param  (bool)   whether or not to encode return before using
	 * @return (string) url
	 **/
	private static function getReturnUrl($return=null, $encode=false)
	{
		// Get the hub url
		$juri    = JURI::getInstance();
		$service = trim($juri->base(), DS);

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		// If someone is logged in already, then we're linking an account, otherwise, we're just loggin in fresh
		$juser = JFactory::getUser();
		$task  = ($juser->get('guest')) ? 'user.login' : 'user.link';

		// Check if a return is specified
		$rtrn = '';
		if (isset($return) && !empty($return))
		{
			if ($encode)
			{
				$return = base64_encode($return);
			}
			$rtrn = "&return=" . $return;
		}

		$url = $service . '/index.php?option=com_users&task=' . $task . '&authenticator=facebook' . $rtrn;

		return $url;
	}
}