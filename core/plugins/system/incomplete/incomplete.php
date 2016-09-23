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
				'com_users.logout',
				'com_users.userlogout',
				'com_support.tickets.save.index',
				'com_support.tickets.new.index',
				'com_members.media.download.profiles',
				'com_members.save.profiles',
				'com_members.profiles.save',
				'com_members.profiles.save.profiles',
				'com_members.changepassword'
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
					Request::setVar('option',     'com_members');
					Request::setVar('controller', 'register');
					Request::setVar('task',       'create');
					Request::setVar('act',        '');

					$this->event->stop();
				}
				else if (substr(User::get('email'), -8) == '@invalid') // force auth_link users to registration update page
				{
					$usersConfig        = Component::params('com_users');
					$simpleRegistration = $usersConfig->get('simple_registration', false);

					if (Session::get('linkaccount', true) && !$simpleRegistration)
					{
						Request::setVar('option', 'com_users');
						Request::setVar('view',   'link');
					}
					else
					{
						Request::setVar('option',     'com_members');
						Request::setVar('controller', 'register');
						Request::setVar('task',       'update');
						Request::setVar('act',        '');
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
						Request::setVar('task',   'view');
						Request::setVar('id',      User::get('id'));
						Request::setVar('active', 'profile');

						$this->event->stop();
					}
				}
			}
		}
	}
}