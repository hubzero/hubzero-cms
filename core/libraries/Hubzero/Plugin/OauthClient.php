<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$service = trim(\Request::base(), '/');

		$task = 'login';
		$option = 'login';

		if (\App::isSite())
		{
			// Legacy support
			if (\App::has('component') && \App::get('component')->isEnabled('com_users'))
			{
				// If someone is logged in already, then we're linking an account
				$task   = (\User::isGuest()) ? 'user.login' : 'user.link';
				$option = 'users';
			}
			else
			{
				$task   = (\User::isGuest()) ? 'login' : 'link';
			}
		}

		$scope = '/index.php?option=com_' . $option . '&task=' . $task . '&authenticator=' . $name;

		return $service . $scope;
	}

	/**
	 * Record that this browser is starting an authorization round trip with
	 * the named provider. Call it from display(), just before redirecting out.
	 *
	 * The callback (login() or link()) is a GET that redeems whatever `code`
	 * it is handed. Without this, a code the attacker obtained for their own
	 * provider account could be redeemed in a victim's session: logged in, it
	 * links the attacker's identity to the victim's account; logged out, it
	 * logs the victim into the attacker's. Providers that echo a per-session
	 * `state` guard against that themselves; this does not depend on the
	 * provider returning anything.
	 *
	 * @param   string  $name  The plugin name
	 * @return  void
	 */
	protected static function beginAuthorization($name)
	{
		\Session::set('started', time(), 'oauthflow.' . $name);
	}

	/**
	 * True once, if this browser began a round trip with the named provider in
	 * the last fifteen minutes. Clears the record either way.
	 *
	 * @param   string  $name  The plugin name
	 * @return  boolean
	 */
	protected static function consumeAuthorization($name)
	{
		$started = (int) \Session::get('started', 0, 'oauthflow.' . $name);
		\Session::clear('started', 'oauthflow.' . $name);

		return ($started > 0 && (time() - $started) < 900);
	}
}
