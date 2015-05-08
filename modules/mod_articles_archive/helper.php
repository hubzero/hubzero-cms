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

namespace Modules\ArticleArchive;

use Hubzero\Module\Module;
use JFactory;
use JHTML;
use stdClass;
use Route;
use Lang;

/**
 * Module class for displaying an article archive
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
		// [!] Legacy compatibility
		$params = $this->params;
		$module = $this->module;

		$params->def('count', 10);
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
		$list = self::getList($params);

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a list of articles
	 *
	 * @param   object  $params  Registry
	 * @return  array
	 */
	public static function getList(&$params)
	{
		// Get database
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('MONTH(created) AS created_month, created, id, title, YEAR(created) AS created_year');
		$query->from('#__content');
		$query->where('state = 2 AND checked_out = 0');
		$query->group('created_year DESC, created_month DESC');

		// Filter by language
		if (JFactory::getApplication()->getLanguageFilter())
		{
			$query->where('language in (' . $db->quote(Lang::getTag()) . ',' . $db->quote('*') . ')');
		}

		$db->setQuery($query, 0, intval($params->get('count')));
		$rows = (array) $db->loadObjectList();

		$app    = JFactory::getApplication();
		$menu   = $app->getMenu();
		$item   = $menu->getItems('link', 'index.php?option=com_content&view=archive', true);
		$itemid = (isset($item) && !empty($item->id) ) ? '&Itemid=' . $item->id : '';

		$i     = 0;
		$lists = array();
		foreach ($rows as $row)
		{
			$date = Date::of($row->created);

			$created_month = $date->format('n');
			$created_year  = $date->format('Y');

			$created_year_cal = Date::of($row->created)->toLocal('Y');
			$month_name_cal   = Date::of($row->created)->toLocal('F');

			$lists[$i] = new stdClass;

			$lists[$i]->link = Route::url('index.php?option=com_content&view=archive&year=' . $created_year . '&month=' . $created_month . $itemid);
			$lists[$i]->text = Lang::txt('MOD_ARTICLES_ARCHIVE_DATE', $month_name_cal, $created_year_cal);

			$i++;
		}
		return $lists;
	}
}
