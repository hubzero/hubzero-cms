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
		if (\JFactory::getApplication()->getClientId() == 0)
		{
			$exceptions = array(
				'com_users.logout',
				'com_support.tickets.save'
			);

			$current  = \JRequest::getWord('option', '');
			$current .= ($controller = \JRequest::getWord('controller', false)) ? '.' . $controller : '';
			$current .= ($task       = \JRequest::getWord('task', false)) ? '.' . $task : '';
			$current .= ($view       = \JRequest::getWord('view', false)) ? '.' . $view : '';

			if (!\JFactory::getUser()->get('guest')
			 && !in_array($current, $exceptions)
			 && \Hubzero\User\Reputation::isJailed())
			{
				\JRequest::setVar('option', 'com_users');
				\JRequest::setVar('view', 'spamjail');
			}
		}
	}
}