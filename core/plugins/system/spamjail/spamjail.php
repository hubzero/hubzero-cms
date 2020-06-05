<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking for spam offences after routing
 */
class plgSystemSpamjail extends \Hubzero\Plugin\Plugin
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
				'com_support.tickets.save.index',
				'com_members.media.download.profiles'
			];

			$current  = Request::getWord('option', '');
			$current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
			$current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

			// If guest, proceed as normal and they'll land on the login page
			if (!in_array($current, $exceptions) && User::getInstance()->reputation->isJailed())
			{
				Request::setVar('option', 'com_members');
				Request::setVar('task', 'spamjail');
			}
		}
	}
}
