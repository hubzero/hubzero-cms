<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\ArticlesCategories;

use Hubzero\Module\Module;
use Components\Categories\Models\Category;
use Component;

/**
 * Module class for displaying a list of categories
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

		$list = self::getList($params);

		if ($list->count())
		{
			$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
			$startLevel = Category::oneOrNew($list->first()->parent_id)->level;
			require $this->getLayoutPath($params->get('layout', 'default'));
		}
	}

	/**
	 * Display module contents
	 *
	 * @param   object  $params  Registry
	 * @return  array
	 */
	public static function getList(&$params)
	{
		require_once Component::path('com_content') . '/site/helpers/route.php';
		require_once Component::path('com_categories') . '/models/category.php';

		$parent = $params->get('parent', 'root');
		if ($parent == 'root')
		{
			$category = Category::all()
				->whereEquals('alias', 'root')
				->row();
		}
		else
		{
			$category = Category::one($parent);
		}

		if ($category != null)
		{
			$items = $category->getChildren();
			if ($params->get('count', 0) > 0 && count($items) > $params->get('count', 0))
			{
				$items = array_slice($items, 0, $params->get('count', 0));
			}
			return $items;
		}
	}
}
