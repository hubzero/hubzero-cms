<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Helpers;

use Submenu;
use Route;
use Lang;
use User;

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
			Lang::txt('COM_INSTALLER_SUBMENU_MANAGE'),
			Route::url('index.php?option=com_installer&controller=manage'),
			$vName == 'manage'
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_PACKAGES'),
			Route::url('index.php?option=com_installer&controller=packages'),
			($vName == 'packages' || $vName == 'repositories')
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_MIGRATIONS'),
			Route::url('index.php?option=com_installer&controller=migrations'),
			$vName == 'migrations'
		);
		/*Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_LANGUAGES'),
			Route::url('index.php?option=com_installer&controller=languages'),
			$vName == 'languages'
		);*/
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_WARNINGS'),
			Route::url('index.php?option=com_installer&controller=warnings'),
			$vName == 'warnings'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object
	 */
	public static function getActions()
	{
		$result = new \Hubzero\Base\Obj;

		$assetName = 'com_installer';

		$actions = \Hubzero\Access\Access::getActionsFromFile(dirname(dirname(__DIR__)) . '/config/access.xml');

		foreach ($actions as $action)
		{
			$result->set($action->name, User::authorise($action->name, $assetName));
		}

		return $result;
	}
}
