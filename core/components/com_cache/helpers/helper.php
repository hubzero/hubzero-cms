<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cache\Helpers;

use Hubzero\Base\ClientManager;
use stdClass;
use Submenu;
use Route;
use Lang;

/**
 * Cache component helper.
 */
class Helper
{
	/**
	 * Get a list of filter options for the application clients.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 */
	static function getClientOptions()
	{
		$items = array();

		foreach (ClientManager::client() as $client)
		{
			$item = new stdClass;
			$item->value = $client->id;
			$item->text  = $client->name;

			$items[] = $item;
		}

		// Build the filter options.
		return $items;
	}

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  The name of the active view.
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		Submenu::addEntry(
			Lang::txt('JGLOBAL_SUBMENU_CHECKIN'),
			Route::url('index.php?option=com_checkin'),
			$vName == 'com_checkin'
		);
		Submenu::addEntry(
			Lang::txt('JGLOBAL_SUBMENU_CLEAR_CACHE'),
			Route::url('index.php?option=com_cache'),
			$vName == 'cache'
		);
		Submenu::addEntry(
			Lang::txt('JGLOBAL_SUBMENU_PURGE_EXPIRED_CACHE'),
			Route::url('index.php?option=com_cache&view=purge'),
			$vName == 'purge'
		);
	}
}
