<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking for unapproved users
 */
class plgSystemUnapproved extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return void
	 */
	public function onAfterRoute()
	{
		if (App::isSite() && !User::isGuest())
		{
			$exceptions = [
				'com_login.logout',
				'com_users.logout',
				'com_users.userlogout',
				'com_support.tickets.save.index',
				'com_support.tickets.new.index',
				'com_members.media.download.profiles'
			];

			$current  = Request::getWord('option', '');
			$current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
			$current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

			// If guest, proceed as normal and they'll land on the login page
			if (!in_array($current, $exceptions) && !User::get('approved'))
			{
				Request::setVar('option', 'com_users');
				Request::setVar('view', 'unapproved');

				$this->event->stop();
			}
		}
	}
}
