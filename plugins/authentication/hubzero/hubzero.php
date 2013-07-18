<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * Authentication plugin for HUBzero
 */
class plgAuthenticationHubzero extends JPlugin
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
	public function plgAuthenticationHubzero(& $subject, $config) {
		parent::__construct($subject, $config);
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
	function onAuthenticate( $credentials, $options, &$response )
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
	function onUserAuthenticate( $credentials, $options, &$response )
	{
		jimport('joomla.user.helper');
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_User_Password');

		// For JLog
		$response->type = 'hubzero';

		// HUBzero does not like blank passwords
		if (empty($credentials['password'])) {
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Empty passwords are not allowed.';
			return false;
		}

		// Initialize variables
		$conditions = '';

		// Get a database object
		$db =& JFactory::getDBO();

		// Determine if attempting to log in via username or email address
		if (strpos($credentials['username'], '@'))
		{
			$conditions = ' WHERE email=' . $db->Quote($credentials['username']);
		}
		else
		{
			$conditions = ' WHERE username=' . $db->Quote($credentials['username']);
		}

		$query = 'SELECT `id`, `username`, `password`'
				. ' FROM `#__users`'
				. $conditions;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (is_array($result) && count($result) > 1)
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = "We're unable to identify your account via email.  Please login using your username.";
			return false;
		}
		elseif(is_array($result) && isset($result[0]))
		{
			$result = $result[0];
		}

		if ($result)
		{	
			if (Hubzero_User_Password::passwordMatches($result->username, $credentials['password'], true)) {
				
				$user = JUser::getInstance($result->id);
				
				$response->username = $user->username;
				$response->email = $user->email;
				$response->fullname = $user->name;
				$response->status = JAUTHENTICATE_STATUS_SUCCESS;
				$response->error_message = '';

				// Check validity and age of password
				ximport('Hubzero_Password_Rule');
				ximport('Hubzero_User_Password');
				$password_rules = Hubzero_Password_Rule::getRules();
				$msg = Hubzero_Password_Rule::validate($credentials['password'], $password_rules, $result->username);
				if(is_array($msg) && !empty($msg[0]))
				{
					$session =& JFactory::getSession();
					$session->set('badpassword', '1');
				}
				if(Hubzero_User_Password::isPasswordExpired($result->username))
				{
					$session =& JFactory::getSession();
					$session->set('expiredpassword', '1');
				}
			} else {
				
				$response->status = JAUTHENTICATE_STATUS_FAILURE;
				$response->error_message = 'Username and password do not match or you do not have an account yet.';
			}
		}
		else {
			
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Username and password do not match or you do not have an account yet.';
		}
	}
}