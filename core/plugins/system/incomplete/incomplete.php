<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2012 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
				'com_members.media.download.profiles'
			];

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

				// Joomla tmp users
				if (User::get('tmp_user'))
				{
					Request::setVar('option',     'com_members');
					Request::setVar('controller', 'register');
					Request::setVar('task',       'create');
					Request::setVar('act',        '');
				}
				else if (substr(User::get('email'), -8) == '@invalid') // force auth_link users to registration update page
				{
					if (Session::get('linkaccount', true))
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
				}
				else // otherwise, send to profile to fill in missing info
				{
					Request::setVar('option', 'com_members');
					Request::setVar('id',      User::get('id'));
					Request::setVar('active', 'profile');
				}
			}

			$this->event->stop();
		}
	}
}