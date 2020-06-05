<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking for missing/required registration fields
 */
class plgSystemIncomplete extends \Hubzero\Plugin\Plugin
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
				'com_members.save.profiles',
				'com_members.profiles.save',
				'com_members.profiles.save.profiles',
				'com_members.changepassword',
				'com_content.article',
				'/legal/terms'
			];

			if ($allowed = trim($this->params->get('exceptions')))
			{
				$allowed = str_replace("\r", '', $allowed);
				$allowed = str_replace('\n', "\n", $allowed);
				$allowed = explode("\n", $allowed);
				$allowed = array_map('trim', $allowed);
				$allowed = array_map('strtolower', $allowed);

				$exceptions = array_merge($exceptions, $allowed);
				$exceptions = array_unique($exceptions);
			}

			$current  = Request::getWord('option', '');
			$current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
			$current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

			// If exception not found, let's try by raw URL path
			if (!in_array($current, $exceptions))
			{
				$current = Request::path();
			}

			if (!in_array($current, $exceptions) && Session::get('registration.incomplete'))
			{
				// First check if we're heading to the registration pages, and allow that through
				if (Request::getWord('option') == 'com_members' && (Request::getWord('controller') == 'register' || Request::getWord('view') == 'register'))
				{
					// Set linkaccount far to false at this point, otherwise we'd get stuck in a loop
					Session::set('linkaccount', false);
					$this->event->stop();
					return;
				}

				// Tmp users
				if (User::get('tmp_user'))
				{
					Request::setVar('option', 'com_members');
					Request::setVar('controller', 'register');
					Request::setVar('task', 'create');
					Request::setVar('act', '');

					$this->event->stop();
				}
				else if (substr(User::get('email'), -8) == '@invalid') // force auth_link users to registration update page
				{
					$usersConfig        = Component::params('com_members');
					$simpleRegistration = $usersConfig->get('simple_registration', false);

					if (Session::get('linkaccount', true) && !$simpleRegistration)
					{
						Request::setVar('option', 'com_users');
						Request::setVar('view', 'link');
					}
					else
					{
						Request::setVar('option', 'com_members');
						Request::setVar('controller', 'register');
						Request::setVar('task', 'update');
						Request::setVar('act', '');
					}

					$this->event->stop();
				}
				else // otherwise, send to profile to fill in missing info
				{
					// Does the user even have access to the profile plugin?
					// If not, then we can't redirect them there
					$plugin = Plugin::byType('members', 'profile');

					if (!empty($plugin))
					{
						Request::setVar('option', 'com_members');
						Request::setVar('task', 'view');
						Request::setVar('id', User::get('id'));
						Request::setVar('active', 'profile');

						$this->event->stop();
					}
					else
					{
						// Nothing else we can do, so let them go
						// and mark the incompleteness state so we don't
						// keep checking on every page load
						Session::get('registration.incomplete', false);
					}
				}
			}
		}
	}
}
