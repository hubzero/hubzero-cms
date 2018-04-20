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

namespace Modules\ArticlesNews;

use Hubzero\Module\Module;
use Components\Content\Models\Article;
use ContentHelperRoute;
use Component;
use Route;
use Event;
use Lang;
use Html;
use User;
use App;

/**
 * Module class for displaying news articles
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

		// Filter by language
		$query->whereEquals('language', App::get('language.filter'));

		// Get ordering &  direction params
		$ordering = str_replace('a.', '', $params->get('ordering', 'a.publish_up'));
		$query->order($ordering, $params->get('direction', 'DESC'));

		//	Retrieve Content
		$items = $query->rows();

		foreach ($items as $item)
		{
			$item->readmore = strlen(trim($item->fulltext));
			$item->slug     = $item->id . ':' . $item->alias;
			$item->catslug  = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = Route::url(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language));
				$item->linkText = Lang::txt('MOD_ARTICLES_NEWS_READMORE');
			}
			else
			{
				$item->link = Route::url('index.php?option=com_users&view=login');
				$item->linkText = Lang::txt('MOD_ARTICLES_NEWS_READMORE_REGISTER');
			}

			$item->introtext = Html::content('prepare', $item->introtext, '', 'mod_articles_news.content');

			if (!$params->get('image'))
			{
				$item->introtext = preg_replace('/<img[^>]*>/', '', $item->introtext);
			}

			$results = Event::trigger('onContentAfterDisplay', array('com_content.article', &$item, &$params, 1));
			$item->afterDisplayTitle = trim(implode("\n", $results));

			$results = Event::trigger('onContentBeforeDisplay', array('com_content.article', &$item, &$params, 1));
			$item->beforeDisplayContent = trim(implode("\n", $results));
		}

		return $items;
	}
}
