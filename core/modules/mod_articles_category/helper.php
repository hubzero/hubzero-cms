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

namespace Modules\ArticlesCategory;

use Hubzero\Module\Module;
use Hubzero\Utility\String;
use ContentHelperRoute;
use Component;
use stdClass;
use Request;
use Route;
use Date;
use User;
use JModelLegacy;

/**
 * Module class for displaying articles in a category
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

		// Prep for Normal or Dynamic Modes
		$mode = $params->get('mode', 'normal');
		$idbase = null;
		switch ($mode)
		{
			case 'dynamic':
				$option = Request::getCmd('option');
				$view   = Request::getCmd('view');
				if ($option === 'com_content')
				{
					switch ($view)
					{
						case 'category':
							$idbase = Request::getInt('id');
							break;
						case 'categories':
							$idbase = Request::getInt('id');
							break;
						case 'article':
							if ($params->get('show_on_article_page', 1))
							{
								$idbase = Request::getInt('catid');
							}
							break;
					}
				}
				break;
			case 'normal':
			default:
				$idbase = $params->get('catid');
				break;
		}

		$cacheid = md5(serialize(array($idbase, $module->module)));

		$cacheparams = new stdClass;
		$cacheparams->cachemode    = 'id';
		$cacheparams->class        = '\Modules\ArticlesCategory\Helper';
		$cacheparams->method       = 'getList';
		$cacheparams->methodparams = $params;
		$cacheparams->modeparams   = $cacheid;

		$list = \Module::cache($module, $params, $cacheparams);

		if (!empty($list))
		{
			$grouped = false;
			$article_grouping = $params->get('article_grouping', 'none');
			$article_grouping_direction = $params->get('article_grouping_direction', 'ksort');
			$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
			$item_heading = $params->get('item_heading');

			if ($article_grouping !== 'none')
			{
				$grouped = true;
				switch ($article_grouping)
				{
					case 'year':
					case 'month_year':
						$list = self::groupByDate($list, $article_grouping, $article_grouping_direction, $params->get('month_year_format', 'F Y'));
						break;
					case 'author':
					case 'category_title':
						$list = self::groupBy($list, $article_grouping, $article_grouping_direction);
						break;
					default:
						break;
				}
			}
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
		require_once PATH_CORE . '/components/com_content/site/router.php';
		require_once PATH_CORE . '/components/com_content/site/helpers/route.php';

		JModelLegacy::addIncludePath(PATH_CORE . '/components/com_content/site/models', 'ContentModel');

		// Get an instance of the generic articles model
		$articles = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$appParams = App::has('params') ? App::get('params') : new \Hubzero\Config\Registry('');
		$articles->setState('params', $appParams);

		// Set the filters based on the module params
		$articles->setState('list.start', 0);
		$articles->setState('list.limit', (int) $params->get('count', 0));
		$articles->setState('filter.published', 1);

		// Access filter
		$access = !Component::params('com_content')->get('show_noauth');
		$authorised = User::getAuthorisedViewLevels();
		$articles->setState('filter.access', $access);

		// Prep for Normal or Dynamic Modes
		$mode = $params->get('mode', 'normal');
		switch ($mode)
		{
			case 'dynamic':
				$option = Request::getCmd('option');
				$view   = Request::getCmd('view');
				if ($option === 'com_content')
				{
					switch ($view)
					{
						case 'category':
							$catids = array(Request::getInt('id'));
							break;
						case 'categories':
							$catids = array(Request::getInt('id'));
							break;
						case 'article':
							if ($params->get('show_on_article_page', 1))
							{
								$article_id = Request::getInt('id');
								$catid      = Request::getInt('catid');

								if (!$catid)
								{
									// Get an instance of the generic article model
									$article = JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request' => true));

									$article->setState('params', $appParams);
									$article->setState('filter.published', 1);
									$article->setState('article.id', (int) $article_id);
									$item = $article->getItem();

									$catids = array($item->catid);
								}
								else
								{
									$catids = array($catid);
								}
							}
							else
							{
								// Return right away if show_on_article_page option is off
								return;
							}
							break;

						case 'featured':
						default:
							// Return right away if not on the category or article views
							return;
					}
				}
				else
				{
					// Return right away if not on a com_content page
					return;
				}

				break;

			case 'normal':
			default:
				$catids = $params->get('catid');
				$articles->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));
				break;
		}

		// Category filter
		if ($catids)
		{
			if ($params->get('show_child_category_articles', 0) && (int) $params->get('levels', 0) > 0)
			{
				// Get an instance of the generic categories model
				$categories = JModelLegacy::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
				$categories->setState('params', $appParams);
				$levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
				$categories->setState('filter.get_children', $levels);
				$categories->setState('filter.published', 1);
				$categories->setState('filter.access', $access);
				$additional_catids = array();

				foreach ($catids as $catid)
				{
					$categories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $categories->getItems($recursive);

					if ($items)
					{
						foreach ($items as $category)
						{
							$condition = (($category->level - $categories->getParent()->level) <= $levels);
							if ($condition)
							{
								$additional_catids[] = $category->id;
							}
						}
					}
				}

				$catids = array_unique(array_merge($catids, $additional_catids));
			}

			$articles->setState('filter.category_id', $catids);
		}

		// Ordering
		$articles->setState('list.ordering', $params->get('article_ordering', 'a.ordering'));
		$articles->setState('list.direction', $params->get('article_ordering_direction', 'ASC'));

		// New Parameters
		$articles->setState('filter.featured', $params->get('show_front', 'show'));
		$articles->setState('filter.author_id', $params->get('created_by', ""));
		$articles->setState('filter.author_id.include', $params->get('author_filtering_type', 1));
		$articles->setState('filter.author_alias', $params->get('created_by_alias', ""));
		$articles->setState('filter.author_alias.include', $params->get('author_alias_filtering_type', 1));
		$excluded_articles = $params->get('excluded_articles', '');

		if ($excluded_articles)
		{
			$excluded_articles = explode("\r\n", $excluded_articles);
			$articles->setState('filter.article_id', $excluded_articles);
			$articles->setState('filter.article_id.include', false); // Exclude
		}

		$date_filtering = $params->get('date_filtering', 'off');
		if ($date_filtering !== 'off')
		{
			$articles->setState('filter.date_filtering', $date_filtering);
			$articles->setState('filter.date_field', $params->get('date_field', 'a.created'));
			$articles->setState('filter.start_date_range', $params->get('start_date_range', '1000-01-01 00:00:00'));
			$articles->setState('filter.end_date_range', $params->get('end_date_range', '9999-12-31 23:59:59'));
			$articles->setState('filter.relative_date', $params->get('relative_date', 30));
		}

		// Filter by language
		$articles->setState('filter.language', \App::get('language.filter'));

		$items = $articles->getItems();

		// Display options
		$show_date        = $params->get('show_date', 0);
		$show_date_field  = $params->get('show_date_field', 'created');
		$show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
		$show_category    = $params->get('show_category', 0);
		$show_hits        = $params->get('show_hits', 0);
		$show_author      = $params->get('show_author', 0);
		$show_introtext   = $params->get('show_introtext', 0);
		$introtext_limit  = $params->get('introtext_limit', 100);

		// Find current Article ID if on an article page
		$option = Request::getCmd('option');
		$view   = Request::getCmd('view');

		if ($option === 'com_content' && $view === 'article')
		{
			$active_article_id = Request::getInt('id');
		}
		else
		{
			$active_article_id = 0;
		}

		// Prepare data for display using display options
		foreach ($items as &$item)
		{
			$item->slug    = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid ? $item->catid . ':' . $item->category_alias : $item->catid;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = Route::url(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->language));
			}
			else
			{
				// Angie Fixed Routing
				$menu = \App::get('menu');
				$menuitems = $menu->getItems('link', 'index.php?option=com_users&view=login');
				if (isset($menuitems[0]))
				{
					$Itemid = $menuitems[0]->id;
				}
				elseif (Request::getInt('Itemid') > 0)
				{
					// Use Itemid from requesting page only if there is no existing menu
					$Itemid = Request::getInt('Itemid');
				}

				$item->link = Route::url('index.php?option=com_users&view=login&Itemid=' . $Itemid);
			}

			// Used for styling the active article
			$item->active = $item->id == $active_article_id ? 'active' : '';

			$item->displayDate = '';
			if ($show_date)
			{
				$item->displayDate = Date::of($item->$show_date_field)->toLocal($show_date_format);
			}

			if ($item->catid)
			{
				$item->displayCategoryLink  = Route::url(ContentHelperRoute::getCategoryRoute($item->catid));
				$item->displayCategoryTitle = $show_category ? '<a href="' . $item->displayCategoryLink . '">' . $item->category_title . '</a>' : '';
			}
			else
			{
				$item->displayCategoryTitle = $show_category ? $item->category_title : '';
			}

			$item->displayHits = $show_hits ? $item->hits : '';
			$item->displayAuthorName = $show_author ? $item->author : '';
			if ($show_introtext)
			{
				$item->introtext = Html::content('prepare', $item->introtext, '', 'mod_articles_category.content');
				$item->introtext = self::_cleanIntrotext($item->introtext);
			}
			$item->displayIntrotext = $show_introtext ? self::truncate($item->introtext, $introtext_limit) : '';
			$item->displayReadmore  = $item->alternative_readmore;
		}

		return $items;
	}

	/**
	 * Clean some unwanted tags out of string
	 *
	 * @param   string  $introtext
	 * @return  string
	 */
	public static function _cleanIntrotext($introtext)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<a><em><strong>');

		$introtext = trim($introtext);

		return $introtext;
	}

	/**
	* Method to truncate introtext
	*
	* The goal is to get the proper length plain text string with as much of
	* the html intact as possible with all tags properly closed.
	*
	* @param   string   $html       The content of the introtext to be truncated
	* @param   integer  $maxLength  The maximum number of charactes to render
	* @return  string   The truncated string
	*/
	public static function truncate($html, $maxLength = 0)
	{
		$baseLength = strlen($html);
		$diffLength = 0;

		// First get the plain text string. This is the rendered text we want to end up with.
		$ptString = String::truncate($html, $maxLength, array('html' => true));

		for ($maxLength; $maxLength < $baseLength;)
		{
			// Now get the string if we allow html.
			$htmlString = String::truncate($html, $maxLength, array('html' => true));

			// Now get the plain text from the html string.
			$htmlStringToPtString = String::truncate($htmlString, $maxLength);

			// If the new plain text string matches the original plain text string we are done.
			if ($ptString == $htmlStringToPtString)
			{
				return $htmlString;
			}
			// Get the number of html tag characters in the first $maxlength characters
			$diffLength = strlen($ptString) - strlen($htmlStringToPtString);

			// Set new $maxlength that adjusts for the html tags
			$maxLength += $diffLength;
			if ($baseLength <= $maxLength || $diffLength <= 0)
			{
				return $htmlString;
			}
		}
		return $html;
	}

	/**
	 * Group items
	 *
	 * @param   array   $list
	 * @param   string  $fieldName
	 * @param   string  $article_grouping_direction
	 * @param   string  $fieldNameToKeep
	 * @return  array
	 */
	public static function groupBy($list, $fieldName, $article_grouping_direction, $fieldNameToKeep = null)
	{
		$grouped = array();

		if (!is_array($list))
		{
			if ($list == '')
			{
				return $grouped;
			}

			$list = array($list);
		}

		foreach ($list as $key => $item)
		{
			if (!isset($grouped[$item->$fieldName]))
			{
				$grouped[$item->$fieldName] = array();
			}

			if (is_null($fieldNameToKeep))
			{
				$grouped[$item->$fieldName][$key] = $item;
			}
			else
			{
				$grouped[$item->$fieldName][$key] = $item->$fieldNameToKeep;
			}

			unset($list[$key]);
		}

		$article_grouping_direction($grouped);

		return $grouped;
	}

	/**
	 * Group items by date
	 *
	 * @param   array   $list
	 * @param   string  $type
	 * @param   string  $article_grouping_direction
	 * @param   string  $month_year_format
	 * @return  array
	 */
	public static function groupByDate($list, $type = 'year', $article_grouping_direction, $month_year_format = 'F Y')
	{
		$grouped = array();

		if (!is_array($list))
		{
			if ($list == '')
			{
				return $grouped;
			}

			$list = array($list);
		}

		foreach ($list as $key => $item)
		{
			switch ($type)
			{
				case 'month_year':
					$month_year = substr($item->created, 0, 7);

					if (!isset($grouped[$month_year]))
					{
						$grouped[$month_year] = array();
					}

					$grouped[$month_year][$key] = $item;
					break;

				case 'year':
				default:
					$year = substr($item->created, 0, 4);

					if (!isset($grouped[$year]))
					{
						$grouped[$year] = array();
					}

					$grouped[$year][$key] = $item;
					break;
			}

			unset($list[$key]);
		}

		$article_grouping_direction($grouped);

		if ($type === 'month_year')
		{
			foreach ($grouped as $group => $items)
			{
				$date = Date::of($group);
				$formatted_group = $date->format($month_year_format);
				$grouped[$formatted_group] = $items;
				unset($grouped[$group]);
			}
		}

		return $grouped;
	}
}
