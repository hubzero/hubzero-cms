<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Plugins\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Access\Access;
use Filesystem;
use Html;
use User;
use App;

/**
 * Plugins component helper.
 */
class Plugins
{
	/**
	 * Extension name
	 *
	 * @var  string
	 */
	public static $extension = 'com_plugins';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  object
	 */
	public static function getActions()
	{
		$result    = new Obj;
		$assetName = self::$extension;

		$actions = Access::getActionsFromFile(\Component::path($assetName) . '/config/access.xml');

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
	public static function stateOptions()
	{
		// Build the active state filter options.
		$options = array();
		$options[] = Html::select('option', '1', 'JENABLED');
		$options[] = Html::select('option', '0', 'JDISABLED');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array
	 */
	public static function folderOptions()
	{
		$db = App::get('db');

		$query = $db->getQuery()
			->select('DISTINCT(folder)', 'value')
			->select('folder', 'text')
			->from('#__extensions')
			->whereEquals('type', 'plugin')
			->order('folder', 'asc');

		$db->setQuery($query->toString());
		$options = $db->loadObjectList();

		if ($error = $db->getErrorMsg())
		{
			App::abort(500, $error);
		}

		return $options;
	}
}
