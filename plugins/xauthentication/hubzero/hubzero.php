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
class plgXAuthenticationHubzero extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param     array  $credentials Array holding the user credentials
	 * @param     array  $options     Array of extra options
	 * @param     object $response    Authentication response object
	 * @return    void
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		ximport('Hubzero_User_Profile');

		// For JLog
		$response->type = 'hubzero';

		if (empty($credentials['password']))
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Can not have a blank password';
			return false;
		}

		$profile = Hubzero_User_Profile::getInstance($credentials['username']);

		if (empty($profile)) 
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Username not found';
			return false;
		}

		$passhash = $profile->get('userPassword');

		if (empty($passhash)) 
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Password not found for requested account';
			return false;
		}

		if (Hubzero_User_Helper::encrypt_password($credentials['password']) != $passhash)
		{
			$response->status = JAUTHENTICATE_STATUS_FAILURE;
			$response->error_message = 'Incorrect username/password';
			return false;
		}

		$response->username = $profile->get('username');
		$response->email = $profile->get('email');
		$response->fullname = $profile->get('name');
		$response->password_clear = $credentials['password'];
		// Were good - So say so.
		$response->status = JAUTHENTICATE_STATUS_SUCCESS;
		$response->error_message = '';
	}
}
