<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Plugin;

/**
 * Extended Plugin for OAuth clients
 */
abstract class OauthClient extends Plugin
{
	/**
	 * Perform logout
	 *
	 * @return  void
	 */
	abstract public function logout();

	/**
	 * Check login status of current user with regards to provider
	 *
	 * @return  array  $status
	 */
	abstract public function status();

	/**
	 * Method to call when redirected back from provider after authentication
	 * Grab the return URL if set and handle denial of app privileges from provider
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	abstract public function login(&$credentials, &$options);

	/**
	 * Method to setup params and redirect to auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	abstract public function display($view, $tpl);

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object   $response     Authentication response object
	 * @return  boolean
	 */
	abstract public function onUserAuthenticate($credentials, $options, &$response);

	/**
	 * Similar to onAuthenticate, except we already have a logged in user, we're just linking accounts
	 *
	 * @param   array  $options
	 * @return  void
	 */
	abstract public function link($options=array());

	/**
	 * Builds the redirect URI based on the current URI and a few other assumptions
	 *
	 * @param   string  $name  The plugin name
	 * @return  string
	 **/
	protected static function getRedirectUri($name)
	{
		// Get the hub url
		$service = trim(Request::base(), '/');

		if (substr($service, -13) == 'administrator')
		{
			$scope = '/index.php?option=com_login&task=login&authenticator=' . $name;
		}
		else
		{
			// If someone is logged in already, then we're linking an account
			$task  = (User::isGuest()) ? 'user.login' : 'user.link';
			$scope = '/index.php?option=com_users&task=' . $task . '&authenticator=' . $name;
		}

		return $service . $scope;
	}
}