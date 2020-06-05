<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$db = App::get('db');

		$query = $db->getQuery()
			->select('MONTH(created)', 'created_month')
			->select('created')
			->select('id')
			->select('title')
			->select('YEAR(created)', 'created_year')
			->from('#__content')
			->whereEquals('state', 2)
			->whereEquals('checked_out', 0)
			->group('created_year')
			->group('created_month');

		// Filter by language
		if (App::get('languag.filter'))
		{
			$query->whereIn('language', array(Lang::getTag(), '*'));
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
