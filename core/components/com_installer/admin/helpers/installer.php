<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin\Helpers;

use Submenu;
use Route;
use Lang;
use User;
use Hubzero\Base\Obj;
use Hubzero\Access\Access;
use Hubzero\Form\Field;
use Filesystem;
use Html;
use App;
use Components\Installer\Admin\Models\Extension;


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
	public static function addSubmenu($vName = 'manage')
	{
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_SUBMENU_CORE'),
			Route::url('index.php?option=com_installer&controller=manage'),
			($vName == 'manage' || $vName == 'migrations')
		);
		Submenu::addEntry(
			Lang::txt('COM_INSTALLER_CUSTOMEXTS_SUBMENU'),
			Route::url('index.php?option=com_installer&controller=customexts'),
			$vName == 'customexts'
		);
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

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array
	 */
	public static function LocationOptions()
	{
		// Build the active state filter options.
		$options = array();
		$options[] = Html::select('option', '0', 'JSITE');
		$options[] = Html::select('option', '1', 'JADMINISTRATOR');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array
	 */
	public static function StatusOptions()
	{
		// Build the active state filter options.
		$options = array();
		$options[] = Html::select('option', '1', 'JENABLED');
		$options[] = Html::select('option', '0', 'JDISABLED');
		$options[] = Html::select('option', '2', 'JPROTECTED');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array
	 */
	public static function TypeOptions()
	{
		$db = App::get('db');

		$query = $db->getQuery()
			->select('DISTINCT(type)', 'value')
			->select('type', 'text')
			->from('#__extension_types')
			->order('type', 'asc');

		$db->setQuery($query->toString());
		$options = $db->loadObjectList();

		if ($error = $db->getErrorMsg())
		{
			App::abort(500, $error);
		}

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array
	 */
	public static function GroupOptions()
	{
		$db = App::get('db');

		$query = $db->getQuery()
			->select('DISTINCT(folder)', 'value')
			->select('folder', 'text')
			->from('#__extensions')
			->where('folder', '!=', '')
			->order('folder', 'asc');

		$db->setQuery($query->toString());
		$options = $db->loadObjectList();

		if ($error = $db->getErrorMsg())
		{
			App::abort(500, $error);
		}

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array
	 */
	public static function VersionOptions()
	{
		// Build the active state filter options.
		$options = array();
		$options[] = Html::select('option', '0', 'Current');
		$options[] = Html::select('option', '1', 'Previous');

		return $options;
	}

}
