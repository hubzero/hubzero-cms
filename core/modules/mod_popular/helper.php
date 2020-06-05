<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Popular;

use Hubzero\Module\Module;
use Components\Content\Models\Article;
use Components\Categories\Models\Category;
use Component;
use Exception;
use Route;
use Lang;
use User;

/**
 * Module class for displaying popular articles
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
		if (!\App::isAdmin())
		{
			return;
		}

		require_once Component::path('com_content') . '/models/article.php';

		// [!] Legacy compatibility
		$params = $this->params;

		// Get module data.
		$list = $this->getList($params);

		// Render the module
		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Get a list of the most popular articles
	 *
	 * @param   object  $params  The module parameters.
	 * @return  array
	 */
	public static function getList($params)
	{
		// Get an instance of the generic articles model
		$query = Article::all();

		// Set Category Filter
		$categoryId = $params->get('catid');
		if (is_numeric($categoryId))
		{
			$query->whereEquals('catid', $categoryId);
		}

		// Set User Filter.
		$userId = User::get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$query->whereEquals('created_by', $userId);
			break;

			case 'not_me':
				$query->where('created_by', '!=', $userId);
			break;
		}

		// Set the Start and Limit
		$query->start(0)
			->limit($params->get('count', 5))
			->order('hits', 'desc');

		$items = $query->rows();

		// Set the links
		foreach ($items as $item)
		{
			if (User::authorise('core.edit', 'com_content.article.' . $item->id))
			{
				$item->link = Route::url('index.php?option=com_content&task=article.edit&id=' . $item->id);
			}
			else
			{
				$item->link = '';
			}
		}

		return $items;
	}

	/**
	 * Get the alternate title for the module
	 *
	 * @param   object  $params  The module parameters.
	 * @return  string  The alternate title for the module.
	 */
	public static function getTitle($params)
	{
		$who   = $params->get('user_id');
		$catid = (int) $params->get('catid');

		if ($catid)
		{
			require_once Component::path('com_categories') . '/models/category.php';

			$category = Category::one($catid);
			if ($category)
			{
				$title = $category->title;
			}
			else
			{
				$title = Lang::txt('MOD_POPULAR_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}

		return Lang::txts('MOD_POPULAR_TITLE' . ($catid ? '_CATEGORY' : '') . ($who!='0' ? "_$who" : ''), (int)$params->get('count'), $title);
	}
}
