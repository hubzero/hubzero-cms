<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Installer\Admin\Helpers;

/**
 * Installer helper.
 */
class Installer
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 * @return  void
	 */
	public static function addSubmenu($vName = 'install')
	{
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_INSTALL'),
			Route::url('index.php?option=com_installer'),
			$vName == 'install'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_UPDATE'),
			Route::url('index.php?option=com_installer&controller=update'),
			$vName == 'update'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_MANAGE'),
			Route::url('index.php?option=com_installer&controller=manage'),
			$vName == 'manage'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_DISCOVER'),
			Route::url('index.php?option=com_installer&controller=discover'),
			$vName == 'discover'
		);
		/*Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_DATABASE'),
			Route::url('index.php?option=com_installer&controller=database'),
			$vName == 'database'
		);*/
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_WARNINGS'),
			Route::url('index.php?option=com_installer&controller=warnings'),
			$vName == 'warnings'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_LANGUAGES'),
			Route::url('index.php?option=com_installer&controller=languages'),
			$vName == 'languages'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object
	 */
	public static function getActions()
	{
		$result = new \Hubzero\Base\Object;

		$assetName = 'com_installer';

		$actions = \JAccess::getActions($assetName);

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, $assetName));
		}

		return $result;
	}
}
