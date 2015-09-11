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