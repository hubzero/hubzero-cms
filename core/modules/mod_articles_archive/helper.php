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

namespace Modules\ArticleArchive;

use Hubzero\Module\Module;
use stdClass;
use Route;
use Lang;
use App;

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
		$db = \App::get('db');

		$query = $db->getQuery(true);
		$query->select('MONTH(created) AS created_month, created, id, title, YEAR(created) AS created_year');
		$query->from('#__content');
		$query->where('state = 2 AND checked_out = 0');
		$query->group('created_year DESC, created_month DESC');

		// Filter by language
		if (App::get('languag.filter'))
		{
			$query->where('language in (' . $db->quote(Lang::getTag()) . ',' . $db->quote('*') . ')');
		}

		$db->setQuery($query, 0, intval($params->get('count')));
		$rows = (array) $db->loadObjectList();

		$menu   = App::get('menu');
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
