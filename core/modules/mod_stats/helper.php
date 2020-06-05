<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Stats;

use Hubzero\Module\Module;
use stdClass;
use Config;
use Date;
use Lang;

/**
 * Module helper class for displaying stats
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		// [!] Legacy comptibility
		$params = $this->params;

		$serverinfo = $params->get('serverinfo');
		$siteinfo   = $params->get('siteinfo');

		$list = self::getList($params);
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a list of stats
	 *
	 * @param   object  $params  Registry
	 * @return  array
	 */
	static function &getList(&$params)
	{
		$db    = \App::get('db');
		$rows  = array();

		$serverinfo = $params->get('serverinfo');
		$siteinfo   = $params->get('siteinfo');
		$counter    = $params->get('counter');
		$increase   = $params->get('increase');

		$i = 0;
		if ($serverinfo)
		{
			$rows[$i] = new stdClass;
			$rows[$i]->title = Lang::txt('MOD_STATS_OS');
			$rows[$i]->data  = substr(php_uname(), 0, 7);
			$i++;

			$rows[$i] = new stdClass;
			$rows[$i]->title = Lang::txt('MOD_STATS_PHP');
			$rows[$i]->data  = phpversion();
			$i++;

			$rows[$i] = new stdClass;
			$rows[$i]->title = Lang::txt('MOD_STATS_MYSQL');
			$rows[$i]->data  = $db->getVersion();
			$i++;

			$rows[$i] = new stdClass;
			$rows[$i]->title = Lang::txt('MOD_STATS_TIME');
			$rows[$i]->data  = Date::of('now')->toLocal('H:i');
			$i++;

			$rows[$i] = new stdClass;
			$rows[$i]->title = Lang::txt('MOD_STATS_CACHING');
			$rows[$i]->data  = Config::get('caching') ? Lang::txt('JENABLED') : Lang::txt('JDISABLED');
			$i++;

			$rows[$i] = new stdClass;
			$rows[$i]->title = Lang::txt('MOD_STATS_GZIP');
			$rows[$i]->data  = Config::get('gzip') ? Lang::txt('JENABLED') : Lang::txt('JDISABLED');
			$i++;
		}

		if ($siteinfo)
		{
			$query = $db->getQuery()
				->select('COUNT(id)', 'count_users')
				->from('#__users');
			$db->setQuery($query->toString());
			$users = $db->loadResult();

			$query = $db->getQuery()
				->select('COUNT(id)', 'count_items')
				->from('#__content')
				->whereEquals('state', '1');
			$db->setQuery($query->toString());
			$items = $db->loadResult();

			$query = $db->getQuery()
				->select('COUNT(id)', 'count_links')
				->from('#__weblinks')
				->whereEquals('state', '1');
			$db->setQuery($query->toString());
			$links = $db->loadResult();

			if ($users)
			{
				$rows[$i] = new stdClass;
				$rows[$i]->title = Lang::txt('MOD_STATS_USERS');
				$rows[$i]->data  = $users;
				$i++;
			}

			if ($items)
			{
				$rows[$i] = new stdClass;
				$rows[$i]->title = Lang::txt('MOD_STATS_ARTICLES');
				$rows[$i]->data  = $items;
				$i++;
			}

			if ($links)
			{
				$rows[$i] = new stdClass;
				$rows[$i]->title = Lang::txt('MOD_STATS_WEBLINKS');
				$rows[$i]->data  = $links;
				$i++;
			}
		}

		if ($counter)
		{
			$query = $db->getQuery()
				->select('SUM(hits)', 'count_hits')
				->from('#__content')
				->whereEquals('state', '1');
			$db->setQuery($query->toString());
			$hits = $db->loadResult();

			if ($hits)
			{
				$rows[$i] = new stdClass;
				$rows[$i]->title = Lang::txt('MOD_STATS_ARTICLES_VIEW_HITS');
				$rows[$i]->data  = $hits + $increase;
				$i++;
			}
		}

		return $rows;
	}
}
