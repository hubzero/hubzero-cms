<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking auth factors after routing
 */
class plgSystemAuthfactors extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return  void
	 */
	public function onAfterRoute()
	{
		if (in_array(App::get('client')->id, $this->params->get('clients', [1])))
		{
			$exceptions = [
				'com_login.logout',
				'com_users.logout'
			];

			$current  = Request::getWord('option', '');
			$current .= ($task = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view = Request::getWord('view', false)) ? '.' . $view : '';

			// If guest, proceed as normal and they'll land on the login page
			if (!User::isGuest() && !in_array($current, $exceptions))
			{
				// Get factor status
				$status = App::get('session')->get('authfactors.status', null);

				if ($status === false)
				{
					// If not a guest, and auth factors checks are done and have failed,
					// log out so we start over
					$logout = 'logout' . ucfirst(App::get('client')->alias);
					self::$logout();
				}
				else if ($status === null)
				{
					// If not a guest, but no factor verification has been completed,
					// procede with auth factor checks as applicable
					$factors = 'factors' . ucfirst(App::get('client')->alias);
					self::$factors();
				}
			}
		}
	}

	/**
	 * Logs out of the admin client
	 *
	 * @return  void
	 **/
	private function logoutAdmin()
	{
		Request::setVar('option', 'com_login');
		Request::setVar('task', 'logout');
	}

	/**
	 * Logs out of the site client
	 *
	 * @return  void
	 **/
	private function logoutSite()
	{
		Request::setVar('option', 'com_users');
		Request::setVar('task', 'user.logout');
		Request::setVar('return', base64_encode('/'));
	}

	/**
	 * Sends to factor input view
	 *
	 * @return  void
	 **/
	private function factorsAdmin()
	{
		Request::setVar('option', 'com_login');
		Request::setVar('task', 'factors');
	}

	/**
	 * Sends to factor input view
	 *
	 * @return  void
	 **/
	private function factorsSite()
	{
		Request::setVar('option', 'com_users');
		Request::setVar('view', 'factors');
	}
}
