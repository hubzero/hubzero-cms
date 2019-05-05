<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use \Hubzero\Notification\Handler;
use \Hubzero\Notification\Storage\Cookie;

/**
 * Factor Auth plugin for certificate based identity verification
 */
class plgAuthfactorsCertificate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Renders the auth factor challenge
	 *
	 * @return string
	 **/
	public function onRenderChallenge()
	{
		// There's not really anything to render for this one, you either have
		// a cert or your don't.  If the user does, we'll just redirect.  Otherwise,
		// perhaps another plugin will give them another option.
		if ($this->isAuthenticated())
		{
			// Update session and reload the current page
			App::get('session')->set('authfactors.status', true);
			App::redirect(Request::current());
		}
		else
		{
			// Update session and reload the current page
			App::get('session')->set('authfactors.status', false);

			// Register an error with the cookie handler so that it outlives session termination
			with(new Handler(new Cookie(1)))->error(Lang::txt('COM_LOGIN_FACTORS_FAILED'));

			App::redirect(Request::current());
		}
	}

	/**
	 * Encapsulates auth check for internal plugin use
	 *
	 * @return  bool
	 */
	private function isAuthenticated()
	{
		return (isset($_SERVER['SSL_CLIENT_S_DN']) && $_SERVER['SSL_CLIENT_S_DN']);
	}
}
