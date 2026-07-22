<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
				'com_login.logout.login',
				'com_users.logout',
				'com_users.userlogout',
				'com_users.logout.login',
				'com_support.tickets.save.index',
				'com_support.tickets.save',
				'com_support.tickets.new.index',
				'com_support.tickets.new',
				'com_members.media.download.profiles'
			];

			$current  = Request::getWord('option', '');
			$current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
			$current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

			// Pull current user data from DB rather than cached session value.
			// Proper fix should reload the session value
			$user = \Hubzero\User\User::oneByUsername(User::get('username'));

			// If guest, proceed as normal and they'll land on the login page
			if (!in_array($current, $exceptions) && !$user->get('approved'))
			{
				$originalOption = Request::getWord('option', '');

				Request::setVar('option', 'com_members');
				Request::setVar('task', 'unapproved');

				$this->event->stop();

				// The site's front-page template only renders the component
				// position on non-default menu items, so the in-place swap above
				// produces no visible output on the home page -- the user just
				// sees the landing page instead of the "account pending approval"
				// notice. On the default menu item, redirect to the real URL so
				// we land on a page whose template shows the component. The
				// $originalOption guard keeps this from firing on the redirect
				// target itself (com_members), so it can't loop.
				if (!in_array($originalOption, array('com_members')))
				{
					$menu = App::get('menu');

					if (is_object($menu->getActive()) && is_object($menu->getDefault())
						&& $menu->getActive()->id == $menu->getDefault()->id)
					{
						App::redirect(Route::url('index.php?option=com_members&task=unapproved'));
					}
				}
			}
		}
	}
}
