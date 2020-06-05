<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\ArticlesPopular;

use Hubzero\Module\Module;
use Components\Content\Models\Article;
use Component;
use Route;
use User;
use App;

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
		// [!] Legacy compatibility
		$params = $this->params;

		$list = self::getList($params);
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}

	/**
	 * Display module contents
	 *
	 * @param   object  $params  Registry
	 * @return  array
	 */
	public static function getList(&$params)
	{
		require_once Component::path('com_content') . '/helpers/route.php';
		require_once Component::path('com_content') . '/models/article.php';

		// Get an instance of the generic articles model
		$query = Article::all();

		// Set application parameters in model
		$appParams = App::has('params') ? App::get('params') : new \Hubzero\Config\Registry('');

		// Set the filters based on the module params
		$query->whereEquals('state', Article::STATE_PUBLISHED);

		if ($params->get('show_front', 1) == 1)
		{
			$query->whereEquals('featured', 1);
		}

		$query->start(0)
			->limit((int) $params->get('count', 5));

		// Access filter
		if (!Component::params('com_content')->get('show_noauth'))
		{
			$query->whereIn('access', User::getAuthorisedViewLevels());
		}

		// Category filter
		$catid = $params->get('catid', array());
		if (!empty($catid))
		{
			$query->whereEquals('catid', $catid);
		}

		// Filter by language
		$query->whereEquals('language', App::get('language.filter'));

		// Ordering
		$query->order('hits', 'desc');

		$items = $query->rows();

		foreach ($items as &$item)
		{
			$item->slug    = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = Route::url(\Components\Content\Site\Helpers\Route::getArticleRoute($item->slug, $item->catslug, $item->language));
			}
			else
			{
				$item->link = Route::url('index.php?option=com_login');
			}
		}

		return $items;
	}
}
