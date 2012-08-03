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

ximport('Hubzero_Auth_Domain');
ximport('Hubzero_Auth_Link');

class plgAuthenticationGoogle extends JPlugin
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
		// Not currently used
	}

	/**
	 * Check login status of current user with regards to google
	 *
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		// Not currently used
	}

	/**
	 * Method to call when redirected back from google after authentication
	 * Grab the return URL if set and handle denial of app privileges from facebook
	 *
	 * @access	public
	 * @param   object	$credentials
	 * @param 	object	$options
	 * @return	void
	 */
	public function login(&$credentials, &$options)
	{
		global $mainframe;

		// Included needed google api class
		require_once(JPATH_SITE.DS.'libraries'.DS.'google-api-php-client'.DS.'src'.DS.'apiClient.php');

		// Check the state for our return variable
		if($return = JRequest::getVar('state', '', 'method', 'base64'))
		{
			$b64dreturn = base64_decode($return);
			if(!JURI::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;

		// Get the hub url
		$juri    =& JURI::getInstance();
		$service = trim($juri->base(), DS);

		// If someone is logged in already, then we're linking an account
		$juser = JFactory::getUser();
		$task  = ($juser->get('guest')) ? 'login' : 'link';

		// Set up the config for the google api instance
		$client = new apiClient();
		$client->setClientId($this->params->get('app_id'));
		$client->setClientSecret($this->params->get('app_secret'));
		$client->setRedirectUri($service . '/index.php?option=com_user&task=' . $task . '&authenticator=google');

		// If we have a code comeing back, the user has authorized our app, and we can authenticate
		if (JRequest::getVar('code', NULL))
		{
			// Authenticate the user
			$client->authenticate();

			// Add the access token to the session
			$jsession =& JFactory::getSession();
			$jsession->set('google.token', $client->getAccessToken());
		}
		else
		{
			// User didn't authorize our app or clicked cancel
			$mainframe->redirect(JRoute::_('index.php?option=com_user&view=login&return=' . $return),
				'To log in via Google, you must authorize the ' . $mainframe->getCfg('sitename') . ' app.', 
				'error');
		}
	}

	/**
	 * Method to setup google params and redirect to google auth URL
	 *
	 * @access	public
	 * @param   object	$view	view object
	 * @param 	object	$tpl	template object
	 * @return	void
	 */
	public function display($view, $tpl)
	{
		global $mainframe;

		// Included needed google api classes
		require_once(JPATH_SITE.DS.'libraries'.DS.'google-api-php-client'.DS.'src'.DS.'apiClient.php');
		require_once(JPATH_SITE.DS.'libraries'.DS.'google-api-php-client'.DS.'src'.DS.'contrib'.DS.'apiOauth2Service.php');

		// Get the hub url
		$juri    =& JURI::getInstance();
		$service = trim($juri->base(), DS);

		// If someone is logged in already, then we're linking an account
		$juser = JFactory::getUser();
		$task  = ($juser->get('guest')) ? 'login' : 'link';

		// Set up the config for the google api instance
		$client = new apiClient();
		$client->setClientId($this->params->get('app_id'));
		$client->setClientSecret($this->params->get('app_secret'));
		$client->setRedirectUri($service . '/index.php?option=com_user&task=' . $task . '&authenticator=google');
		$client->setAccessType('online');
		$client->setState($view->return);
		$client->setApprovalPrompt('auto');
		$client->setScopes('https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');

		// Create Oauth2 instance
		$oauth2 = new apiOauth2Service($client);

		// Create and follow the authorization URL
		$authUrl = $client->createAuthUrl();
		$mainframe->redirect($authUrl);
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
		// Included needed google api classes
		require_once(JPATH_SITE.DS.'libraries'.DS.'google-api-php-client'.DS.'src'.DS.'apiClient.php');
		require_once(JPATH_SITE.DS.'libraries'.DS.'google-api-php-client'.DS.'src'.DS.'contrib'.DS.'apiOauth2Service.php');

		// Set up the config for the google api instance
		$client = new apiClient();
		$client->setClientId($this->params->get('app_id'));
		$client->setClientSecret($this->params->get('app_secret'));

		// Create OAuth2 Instance
		$oauth2 = new apiOauth2Service($client);

		// Check if there's an active token in the session
		$jsession =& JFactory::getSession();
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
			$hzal = Hubzero_Auth_Link::find_or_create('authentication', 'google', null, $username);
			$hzal->email = $user_profile['email'];

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'google';
			$response->status    = JAUTHENTICATE_STATUS_SUCCESS;
			$response->fullname  = $user_profile['name'];

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
			}

			$hzal->update();

		}
		else
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Not Authenticated.';
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
		global $mainframe;

		// Included needed google api classes
		require_once(JPATH_SITE.DS.'libraries'.DS.'google-api-php-client'.DS.'src'.DS.'apiClient.php');
		require_once(JPATH_SITE.DS.'libraries'.DS.'google-api-php-client'.DS.'src'.DS.'contrib'.DS.'apiOauth2Service.php');

		$juser = JFactory::getUser();

		// Get the hub url
		$juri    =& JURI::getInstance();
		$service = trim($juri->base(), DS);

		// Set up the config for the google api instance
		$client = new apiClient();
		$client->setClientId($this->params->get('app_id'));
		$client->setClientSecret($this->params->get('app_secret'));
		$client->setRedirectUri($service . '/index.php?option=com_user&task=link&authenticator=google');

		// Create OAuth2 Instance
		$oauth2 = new apiOauth2Service($client);

		// If we have this code, we know we have a successful return from google
		if (JRequest::getVar('code', NULL))
		{
			// Authenticate the user
			$client->authenticate();
		}

		// If we have an access token set, carry on
		if ($client->getAccessToken())
		{
			// Get the user info
			$user_profile = $oauth2->userinfo->get();

			// Make sure we use something unique and consistent here!
			$username = $user_profile['email'];

			// Create the hubzero auth link
			$hzal = Hubzero_Auth_Link::find_or_create('authentication', 'google', null, $username);
			$hzal->user_id = $juser->get('id');
			$hzal->email   = $user_profile['email'];
			$hzal->update();
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel...
			$mainframe->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'),
				'To log in via Google, you must authorize the ' . $mainframe->getCfg('sitename') . ' app.', 
				'error');
		}
	}
}