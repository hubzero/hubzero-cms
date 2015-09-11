<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$query = $db->getQuery(true);

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
			$query->select('COUNT(id) AS count_users');
			$query->from('#__users');
			$db->setQuery($query);
			$users = $db->loadResult();

			$query->clear();
			$query->select('COUNT(id) AS count_items');
			$query->from('#__content');
			$query->where('state = 1');
			$db->setQuery($query);
			$items = $db->loadResult();

			$query->clear();
			$query->select('COUNT(id) AS count_links ');
			$query->from('#__weblinks');
			$query->where('state = 1');
			$db->setQuery($query);
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
			$query->clear();
			$query->select('SUM(hits) AS count_hits');
			$query->from('#__content');
			$query->where('state = 1');
			$db->setQuery($query);
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
