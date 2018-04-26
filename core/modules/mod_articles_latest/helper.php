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

namespace Modules\ArticlesLatest;

use Hubzero\Module\Module;
use Hubzero\Utility\Arr;
use Components\Content\Models\Article;
use ContentHelperRoute;
use Component;
use Route;
use Date;
use User;
use App;

/**
 * Module class for displaying latest articles
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
		require_once Component::path('com_content') . '/site/helpers/route.php';
		require_once Component::path('com_content') . '/models/article.php';

		// Get an instance of the generic articles model
		$query = Article::all();

		// Set the filters based on the module params
		$query->whereEquals('state', Article::STATE_PUBLISHED);
		$query->start(0);
		$query->limit((int) $params->get('count', 5));

		// Access filter
		$access = !Component::params('com_content')->get('show_noauth');
		$authorised = User::getAuthorisedViewLevels();

		// Category filter
		$catids = $params->get('catid', array());
		if (!empty($catids))
		{
			$query->whereIn('catid', $catids);
		}

		// User filter
		$userId = User::get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$query->whereEquals('created_by', (int) $userId);
				break;
			case 'not_me':
				$query->where('created_by', '!=', (int) $userId);
				break;
			case '0':
				break;
			default:
				$query->whereEquals('created_by', (int) $userId);
				break;
		}

		// Filter by language
		$query->whereEquals('language', App::get('language.filter'));

		//  Featured switch
		switch ($params->get('show_featured'))
		{
			case '1':
				$query->whereEquals('featured', 1);
				break;
			case '0':
				$query->whereEquals('featured', 0);
				break;
			default:
				break;
		}

		// Set ordering
		switch ($params->get('ordering'))
		{
			case 'm_dsc':
				$query->order('modified', 'desc')
					->order('created', 'asc');
			break;
			case 'mc_dsc':
				$query->order('CASE WHEN (modified = ' . $db->quote(Date::toSql()) . ') THEN created ELSE modified END', 'desc');
			break;
			case 'c_dsc':
				$query->order('created', 'desc');
			break;
			case 'p_dsc':
			default:
				$query->order('publish_up', 'desc');
			break;
		}

		$items = $query->rows();

		foreach ($items as $item)
		{
			$item->slug    = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = Route::url(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->language));
			}
			else
			{
				$item->link = Route::url('index.php?option=com_users&view=login');
			}
		}

		return $items;
	}
}
