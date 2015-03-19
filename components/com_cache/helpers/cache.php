<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cache\Helpers;

use JSubMenuHelper;
use JHtml;

/**
 * Cache component helper.
 */
class Cache
{
	/**
	 * Get a list of filter options for the application clients.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 */
	static function getClientOptions()
	{
		// Build the filter options.
		return array(
			JHtml::_('select.option', '0', \Lang::txt('JSITE')),
			JHtml::_('select.option', '1', \Lang::txt('JADMINISTRATOR'))
		);
	}

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  The name of the active view.
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			\Lang::txt('JGLOBAL_SUBMENU_CHECKIN'),
			\Route::url('index.php?option=com_checkin'),
			$vName == 'com_checkin'
		);
		JSubMenuHelper::addEntry(
			\Lang::txt('JGLOBAL_SUBMENU_CLEAR_CACHE'),
			\Route::url('index.php?option=com_cache'),
			$vName == 'cache'
		);
		JSubMenuHelper::addEntry(
			\Lang::txt('JGLOBAL_SUBMENU_PURGE_EXPIRED_CACHE'),
			\Route::url('index.php?option=com_cache&view=purge'),
			$vName == 'purge'
		);
	}
}
