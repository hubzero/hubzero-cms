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
		global $mainframe;

		// Inlucded needed facebook sdk class
		require_once(JPATH_SITE.DS.'libraries'.DS.'facebook-php-sdk'.DS.'facebook.php');

		// Set up the config for the sdk instance
		$config           = array();
		$config['appId']  = $this->params->get('app_id');
		$config['secret'] = $this->params->get('app_secret');

		// Create instance and get the facebook user_id
		$facebook = new Facebook($config);

		// Get the hub url
		$juri    =& JURI::getInstance();
		$service = trim($juri->base(), DS);

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		$return = ($view->return) ? "&return=" . $view->return : "&return=" . base64_encode("/login");

		// Set the post fb logout url to be the hub logout url
		// @FIXME: should hub logout log out of facebook too?
		// If not, then it seems like things will work a bit differently than pucas
		// It must be that facebook app info is stored in the user session, and hub logout kills that?
		// Either way, as soon as hub logout is done, we don't have access anymore to check if the user is logged on
		$params = array('next' => $service . '/index.php?option=com_user&task=logout' . $return);

		// Get the logout URL
		$logoutUrl = $facebook->getLogoutUrl($params);

		// Redirect to the logout URL
		$mainframe->redirect($logoutUrl);
	}

	/**
	 * Check login status of current user with regards to facebook
	 *
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		// Inlucded needed facebook sdk class
		require_once(JPATH_SITE.DS.'libraries'.DS.'facebook-php-sdk'.DS.'facebook.php');

		// Set up the config for the sdk instance
		$config           = array();
		$config['appId']  = $this->params->get('app_id');
		$config['secret'] = $this->params->get('app_secret');

		// Create instance and get the facebook user_id
		$facebook = new Facebook($config);

		$status = array();

		if($facebook->getUser())
		{
			$user = $facebook->api('/me?fields=name','GET');
			$status['username'] = $user['name'];
		}

		return $status;
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
		global $mainframe;

		if($return = JRequest::getVar('return', '', 'method', 'base64'))
		{
			$b64dreturn = base64_decode($return);
			if(!JURI::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;

		// Check to make sure they didn't deny our application permissions
		if(JRequest::getVar('error', NULL))
		{
			// User didn't authorize our app or clicked cancel
			$mainframe->redirect(JRoute::_('index.php?option=com_user&view=login&return=' . $return),
				'To log in via Facebook, you must authorize the ' . $mainframe->getCfg('sitename') . ' app.', 
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
		global $mainframe;

		// Included needed facebook sdk class
		require_once(JPATH_SITE.DS.'libraries'.DS.'facebook-php-sdk'.DS.'facebook.php');

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
		$task  = ($juser->get('guest')) ? 'login' : 'link';

		// Set up the config for the facebook sdk instance
		$config               = array();
		$config['appId']      = $this->params->get('app_id');
		$config['secret']     = $this->params->get('app_secret');
		$config['fileUpload'] = false;

		// Create facebook instance
		$facebook = new Facebook($config);

		// Set up params for the login call
		$params = array(
			'scope'        => 'email,user_birthday', // this is where you would specify more information from the facebook profile
			'display'      => 'page',
			'redirect_uri' => $service . '/index.php?option=com_user&task=' . $task . '&authenticator=facebook' . $return
		);

		// Get the login URL
		$loginUrl = $facebook->getLoginUrl($params);

		// Redirect to the login URL
		$mainframe->redirect($loginUrl);
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
		// Inlucded needed facebook sdk class
		require_once(JPATH_SITE.DS.'libraries'.DS.'facebook-php-sdk'.DS.'facebook.php');

		// Set up the config for the sdk instance
		$config           = array();
		$config['appId']  = $this->params->get('app_id');
		$config['secret'] = $this->params->get('app_secret');

		// Create instance and get the facebook user_id
		$facebook = new Facebook($config);
		$user_id  = $facebook->getUser();

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if($user_id > 0)
		{
			// Get the facebook graph api profile for the user
			$user_profile = $facebook->api('/me','GET');

			// Get unique username (we'll use facebook id - could also use facebook username, but not everyone has one defined)
			$username = $user_profile['id'];

			// Create the hubzero auth link
			$hzal = Hubzero_Auth_Link::find_or_create('authentication', 'facebook', null, $username);
			$hzal->email = $user_profile['email'];

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'facebook';
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
		global $mainframe;

		// Inlucded needed facebook sdk class
		require_once(JPATH_SITE.DS.'libraries'.DS.'facebook-php-sdk'.DS.'facebook.php');

		$juser = JFactory::getUser();

		// Set up the config for the sdk instance
		$config           = array();
		$config['appId']  = $this->params->get('app_id');
		$config['secret'] = $this->params->get('app_secret');

		// Create instance and get the facebook user_id
		$facebook = new Facebook($config);
		$user_id  = $facebook->getUser();

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if($user_id > 0)
		{
			// Get the facebook graph api profile for the user
			$user_profile = $facebook->api('/me','GET');

			// Get unique username
			$username = $user_profile['id'];

			$hzad = Hubzero_Auth_Domain::getInstance('authentication', 'facebook', '');

			// Create the link
			if(Hubzero_Auth_Link::getInstance($hzad->id, $username))
			{
				// This facebook account is already linked to another hub account
				$mainframe->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'), 
					'This facebook account appears to already be linked to a hub account', 
					'error');
			}
			else
			{
				$hzal = Hubzero_Auth_Link::find_or_create('authentication', 'facebook', null, $username);
				$hzal->user_id = $juser->get('id');
				$hzal->email   = $user_profile['email'];
				$hzal->update();
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel
			$mainframe->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'), 
				'To link the current account with your Facebook account, you must authorize the ' . $mainframe->getCfg('sitename') . ' app.', 
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
	public function getInfo($params)
	{
		// Inlucded needed facebook sdk class
		require_once(JPATH_SITE.DS.'libraries'.DS.'facebook-php-sdk'.DS.'facebook.php');

		// Set up the config for the sdk instance
		$params = explode("\n", trim($params));
		foreach ($params as $k => $v)
		{
			list($key, $value) = explode("=", $v);
			$p[$key] = $value;
		}
		$config           = array();
		$config['appId']  = $p['app_id'];
		$config['secret'] = $p['app_secret'];

		// Create instance and get the facebook user_id
		$facebook = new Facebook($config);
		$user_id  = $facebook->getUser();

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if($user_id > 0)
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
}