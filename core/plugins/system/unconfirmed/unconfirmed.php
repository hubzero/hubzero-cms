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
			// Complete a pending email confirmation as soon as the user logs in.
			// The confirm code is stashed in the session when the confirmation
			// link is clicked while logged out (see com_members register/confirm),
			// so it survives the login round-trip even when the URL 'return' is
			// lost. Only act if the code matches this user's own pending token.
			$pending = Session::get('members.confirmcode');
			if ($pending)
			{
				Session::set('members.confirmcode', null); // one-shot; avoid loops
				$activation = User::one(User::get('id'))->get('activation');
				if ($activation < 0 && (int) $activation === -(int) $pending)
				{
					App::redirect(Route::url('index.php?option=com_members&controller=register&task=confirm&confirm=' . (int) $pending, false));
					return;
				}
			}

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

			// Always let the registration/confirmation controller through, no
			// matter what view is (or isn't) on the request. The hardcoded
			// exceptions above match on the exact option.controller.task.view
			// string, so a stray view segment silently broke the confirm/resend
			// exemptions and trapped the user in a redirect they couldn't escape.
			$isRegister = (Request::getWord('option') == 'com_members'
				&& (Request::getWord('controller') == 'register' || Request::getWord('view') == 'register'));

			$id = User::get('id');
			$activation = User::one($id)->get('activation');

			if (User::get('id')
			&& ($activation != 1)
			&& ($activation != 3)
			&& !$isRegister
			&& !in_array($current, $exceptions))
			{
				$originalOption = Request::getWord('option', '');

				Request::setVar('option', 'com_members');
				Request::setVar('controller', 'register');
				Request::setVar('task', 'unconfirmed');

				$this->event->stop();

				// The site's front-page template only renders the component
				// position on non-default menu items, so the in-place swap above
				// produces no visible output on the home page — the user just
				// sees the landing page instead of the "confirm your email"
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
						App::redirect(Route::url('index.php?option=com_members&controller=register&task=unconfirmed'));
					}
				}
			}
		}
	}
}
