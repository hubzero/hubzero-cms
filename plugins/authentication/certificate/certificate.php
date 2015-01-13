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

jimport('joomla.plugin.plugin');

/**
 * Auth plugin for certificate based authentication
 */
class plgAuthenticationCertificate extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 */
	public function plgAuthenticationCertificate(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Actions to perform when logging out a user session
	 *
	 * @return void
	 */
	public function logout()
	{
		// Nothing here...certificate authentication only relies on the default
		// HUBzero session for session handling
	}

	/**
	 * Check login status of current user with regards to their client certificate
	 *
	 * @access	public
	 * @return	Array $status
	 */
	public function status()
	{
		$status = array();

		if ($this->isAuthenticated())
		{
			$status['username'] = $_SERVER['SSL_CLIENT_S_DN_CN'];
		}

		return $status;
	}

	/**
	 * Actions to perform when logging in a user session
	 *
	 * @param  array $credentials login credentials
	 * @param  array $options     login options
	 * @return void
	 */
	public function login(&$credentials, &$options)
	{
		// Check for return param
		if ($return = JRequest::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);
			if (!JURI::isInternal($return))
			{
				$return = '';
			}
		}

		$options['return'] = $return;
	}

	/**
	 * Method to setup and redirect to certificate auth URL
	 *
	 * @param  object $view view object
	 * @param  object $tpl  template object
	 * @return void
	 */
	public function display($view, $tpl)
	{
		$return = '';
		if ($view->return)
		{
			$return = '&return=' . $view->return;
		}

		// If someone is logged in already, then we're linking an account, otherwise, we're just loggin in fresh
		$task  = (\JFactory::getUser()->get('guest')) ? 'user.login' : 'user.link';

		\JFactory::getApplication()->redirect('/index.php?option=com_users&task=' . $task . '&authenticator=certificate' . $return);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param  array  $credentials the user credentials
	 * @param  array  $options     any extra options
	 * @param  object $response    authentication response object
	 * @return void
	 * @deprecated 1.3.1
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		return $this->onUserAuthenticate($credentials, $options, $response);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param  array  $credentials the user credentials
	 * @param  array  $options     any extra options
	 * @param  object $response    authentication response object
	 * @return void
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		// Check for the required subject dn field
		if (isset($_SERVER['SSL_CLIENT_S_DN']) && $_SERVER['SSL_CLIENT_S_DN'])
		{
			$domain   = $_SERVER['SSL_CLIENT_I_DN_CN'];
			$username = $_SERVER['SSL_CLIENT_S_DN_CN'];

			$method = (\JComponentHelper::getParams('com_users')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal   = \Hubzero\Auth\Link::$method('authentication', 'certificate', $domain, $username);

			if ($hzal === false)
			{
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Unknown user and new user registration is not permitted.';
				return;
			}

			$hzal->email = $_SERVER['SSL_CLIENT_S_DN_Email'];

			$response->auth_link = $hzal;
			$response->type      = 'certificate';
			$response->status    = JAUTHENTICATE_STATUS_SUCCESS;
			$response->fullname  = $username;

			// Try to deduce fullname from potential patern (ex: LAST.FIRST.MIDDLE.ID)
			if (preg_match('/([[:alpha:]]*)\.([[:alpha:]]*)\.([[:alpha:]]*)/', $username, $matches))
			{
				$response->fullname = ucfirst($matches[2]) . ' ' . ucfirst($matches[3]) . ' ' . ucfirst($matches[1]);
			}

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
				JFactory::getSession()->set('auth_link.tmp_username', $username);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs                  = array();
				$prefs['user_id']       = $user->get('id');
				$prefs['user_img']      = \Hubzero\User\Profile::getInstance($user->get('id'))->getPicture(0, false);
				$prefs['authenticator'] = 'certificate';

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
	 * @param  array $options additional options
	 * @return void
	 */
	public function link($options=array())
	{
		// Check for the required subject dn field
		if ($this->isAuthenticated())
		{
			$domain   = $_SERVER['SSL_CLIENT_I_DN_CN'];
			$username = $_SERVER['SSL_CLIENT_S_DN_CN'];
			$juser    = \JFactory::getUser();

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'certificate', $domain);

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This certificate account is already linked to another hub account
				\JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=account'),
					'This certificate appears to already be linked to a hub account',
					'error');
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'certificate', $domain, $username);
				$hzal->user_id = $juser->get('id');
				$hzal->email   = $_SERVER['SSL_CLIENT_S_DN_Email'];
				$hzal->update();
			}
		}
		else
		{
			// User somehow got redirect back without being authenticated (not sure how this would happen?)
			\JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_members&id=' . \JFactory::getUser()->get('id') . '&active=account'),
				'There was an error linking your certificate, please try again later.',
				'error');
		}
	}

	/**
	 * Encapsulates auth check for internal plugin use
	 *
	 * @return bool
	 **/
	private function isAuthenticated()
	{
		return (isset($_SERVER['SSL_CLIENT_S_DN']) && $_SERVER['SSL_CLIENT_S_DN']);
	}
}