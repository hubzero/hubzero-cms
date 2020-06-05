<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking for unconfirmed user emails
 */
class plgSystemUnconfirmed extends \Hubzero\Plugin\Plugin
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
				'com_login.logout.login',
				'com_users.logout',
				'com_users.userlogout',
				'com_users.logout.login',
				'com_support.tickets.save.index',
				'com_support.tickets.new.index',
				'com_members.media.download.profiles',
				'com_members.register.unconfirmed.profiles',
				'com_members.register.change.profiles',
				'com_members.register.resend.profiles',
				'com_members.register.resend',
				'com_members.register.confirm.profiles',
				'com_members.register.confirm',
				'com_members.save.profiles',
				'com_members.profiles.save',
				'com_members.profiles.save.profiles',
				'com_members.changepassword'
			];

			$current  = Request::getWord('option', '');
			$current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
			$current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

			$id = User::get('id');
			$activation = User::one($id)->get('activation');

			if (User::get('id')
			&& ($activation != 1)
			&& ($activation != 3)
			&& !in_array($current, $exceptions))
			{
				Request::setVar('option', 'com_members');
				Request::setVar('controller', 'register');
				Request::setVar('task', 'unconfirmed');

				$this->event->stop();
			}
		}
	}
}
