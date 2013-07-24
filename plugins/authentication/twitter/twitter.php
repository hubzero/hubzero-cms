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

// Include php library
require_once(join(DS, array( JPATH_ROOT, 'libraries', 'twitteroauth', 'twitteroauth.php' )));

class plgAuthenticationTwitter extends JPlugin
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
	function plgAuthenticationTwitter(& $subject, $config)
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
		// @TODO: implement me
	}

	/**
	 * Check login status of current user with regards to twitter
	 *
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		// @TODO: implement me
	}

	/**
	 * Method to call when redirected back from twitter after authentication
	 * Grab the return URL if set and handle denial of app privileges from twitter
	 *
	 * @access	public
	 * @param   object	$credentials
	 * @param 	object	$options
	 * @return	void
	 */
	public function login(&$credentials, &$options)
	{
		$app = JFactory::getApplication();

		if ($return = JRequest::getVar('return', '', 'method', 'base64'))
		{
			$b64dreturn = base64_decode($return);
			if (!JURI::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;
		$com_user = (version_compare(JVERSION, '2.5', 'ge')) ? 'com_users' : 'com_user';

		// Check to make sure they didn't deny our application permissions
		if (JRequest::getWord('denied', false))
		{
			// User didn't authorize our app or clicked cancel
			$app->redirect(JRoute::_('index.php?option=' . $com_user . '&view=login&return=' . $return),
				'To log in via Twitter, you must authorize the ' . $app->getCfg('sitename') . ' app.', 
				'error');
			return;
		}
	}

	/**
	 * Method to setup twitter params and redirect to twitter auth URL
	 *
	 * @access	public
	 * @param   object	$view	view object
	 * @param 	object	$tpl	template object
	 * @return	void
	 */
	public function display($view, $tpl)
	{
		$app = JFactory::getApplication();

		// Get the hub url
		$juri    =& JURI::getInstance();
		$service = trim($juri->base(), DS);

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		// Check if a return is specified
		if ($view->return)
		{
			$return = "&return=" . $view->return;
		}

		// If someone is logged in already, then we're linking an account, otherwise, we're just loggin in fresh
		$juser = JFactory::getUser();
		if (version_compare(JVERSION, '2.5', 'ge'))
		{
			$com_user = 'com_users';
			$task     = ($juser->get('guest')) ? 'user.login' : 'user.link';
		}
		else
		{
			$com_user = 'com_user';
			$task     = ($juser->get('guest')) ? 'login' : 'link';
		}

		// Build twitter object
		$twitter = new TwitterOAuth($this->params->get('app_id'), $this->params->get('app_secret'));

		// Set callback url and get temp credentials
		$callback = $service . '/index.php?option=' . $com_user . '&task=' . $task . '&authenticator=twitter' . $return;
		$temporary_credentials = $twitter->getRequestToken($callback);

		// Store temp credentials in session for use after authentication redirect from twitter
		JFactory::getSession()->set('twitter.oauth.token', $temporary_credentials['oauth_token']);
		JFactory::getSession()->set('twitter.oauth.token_secret', $temporary_credentials['oauth_token_secret']);

		// Get login url
		$redirect_url = $twitter->getAuthorizeURL($temporary_credentials);

		// Redirect to the login URL
		$app->redirect($redirect_url);
		return;
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
	public function onAuthenticate( $credentials, $options, &$response )
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
		// Build twitter object using temp credentials saved in session
		$twitter = new TwitterOAuth(
			$this->params->get('app_id'),
			$this->params->get('app_secret'),
			JFactory::getSession()->get('twitter.oauth.token'),
			JFactory::getSession()->get('twitter.oauth.token_secret')
		);

		// Request user specific (longer lasting) credentials
		$token_credentials = $twitter->getAccessToken(JRequest::getVar('oauth_verifier'));

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
			$hzal = Hubzero_Auth_Link::find_or_create('authentication', 'twitter', null, $username);

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'twitter';
			$response->status    = JAUTHENTICATE_STATUS_SUCCESS;
			$response->fullname  = $account->name;

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
				JFactory::getSession()->set('auth_link.tmp_username', $account->screen_name);
			}

			$hzal->update();
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
	 * @return	void
	 */
	public function link()
	{
		$app = JFactory::getApplication();

		$juser = JFactory::getUser();

		// Build twitter object using temp credentials saved in session
		$twitter = new TwitterOAuth(
			$this->params->get('app_id'),
			$this->params->get('app_secret'),
			JFactory::getSession()->get('twitter.oauth.token'),
			JFactory::getSession()->get('twitter.oauth.token_secret')
		);

		// Request user specific (longer lasting) credentials
		$token_credentials = $twitter->getAccessToken(JRequest::getVar('oauth_verifier'));

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

			$hzad = Hubzero_Auth_Domain::getInstance('authentication', 'twitter', '');

			// Create the link
			if (Hubzero_Auth_Link::getInstance($hzad->id, $username))
			{
				// This twitter account is already linked to another hub account
				$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'), 
					'This Twitter account appears to already be linked to a hub account', 
					'error');
				return;
			}
			else
			{
				$hzal = Hubzero_Auth_Link::find_or_create('authentication', 'twitter', null, $username);
				$hzal->user_id = $juser->get('id');
				$hzal->update();
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel
			$app->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'), 
				'To link the current account with your Twitter account, you must authorize the ' . $app->getCfg('sitename') . ' app.', 
				'error');
			return;
		}
	}
}