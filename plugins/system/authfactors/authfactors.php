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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * System plugin checking auth factors after routing
 */
class plgSystemAuthfactors extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return void
	 */
	public function onAfterRoute()
	{
		$exceptions = [
			'com_login.logout'
		];

		$current = Request::getWord('option', '') . '.' . Request::getWord('task', '');

		// If guest, proceed as normal and they'll land on the login page
		if (!User::isGuest() && !in_array($current, $exceptions))
		{
			// Get factor status
			$status = \JFactory::getSession()->get('authfactors.status', null);

			if ($status === false)
			{
				// If not a guest, and auth factors checks are done and have failed,
				// log out so we start over
				Request::setVar('option', 'com_login');
				Request::setVar('task', 'logout');

				return false;
			}
			else if ($status === null)
			{
				// If not a guest, but no factor verification has been completed,
				// procede with auth factor checks as applicable
				Request::setVar('option', 'com_login');
				Request::setVar('task', 'factors');
			}
		}
	}
}