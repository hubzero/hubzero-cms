<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Latest;

use Hubzero\Module\Module;
use Hubzero\Utility\Str;
use Components\Content\Models\Article;
use Components\Categories\Models\Category;
use Component;
use stdClass;
use Request;
use Route;
use Date;
use User;
use App;

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

		$list = self::getList($params); //\Module::cache($module, $params, $cacheparams);

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
		require_once Component::path('com_content') . '/site/router.php';
		require_once Component::path('com_content') . '/site/helpers/route.php';
		require_once Component::path('com_content') . '/models/article.php';

		// Set application parameters in model
		$appParams = App::has('params') ? App::get('params') : new \Hubzero\Config\Registry('');

		// Set the filters based on the module params
		$query = Article::all();
		$query->whereEquals('state', Article::STATE_PUBLISHED);

		// Access filter
		if (!Component::params('com_content')->get('show_noauth'))
		{
			$query->whereIn('access', User::getAuthorisedViewLevels());
		}

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
									$article = Article::all();
									$article->whereEquals('published', Article::STATE_PUBLISHED);
									$article->whereEquals('id', (int) $article_id);

									$item = $article->row();

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
				break;
		}

		// Category filter
		if ($catids)
		{
			if ($params->get('show_child_category_articles', 0) && (int) $params->get('levels', 0) > 0)
			{
				require_once Component::path('com_category') . '/models/category.php';

				// Get an instance of the generic categories model
				$categories = Category::all();
				$levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
				$categories->where('level', '<=', $levels);
				$categories->whereEquals('published', Category::STATE_PUBLISHED);
				if (!Component::params('com_content')->get('show_noauth'))
				{
					$categories->whereIn('access', User::getAuthorisedViewLevels());
				}
				$additional_catids = array();

				foreach ($catids as $catid)
				{
					$catgories = clone $categories;
					$catgories->whereEquals('parent_id', $catid);

					$items = $catgories->rows();

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

			$query->whereIn('catid', $catids);
		}

		// Ordering
		$ordering = str_replace('a.', '', $params->get('article_ordering', 'a.ordering'));
		$query->order($ordering, $params->get('article_ordering_direction', 'ASC'));

		// New Parameters
		if ($params->get('show_front', 'show') == 'hide')
		{
			$query->whereEquals('featured', 0);
		}
		if ($params->get('show_front', 'show') == 'only')
		{
			$query->whereEquals('featured', 1);
		}

		if ($creator = $params->get('created_by', ''))
		{
			$query->whereEquals('created_by', $creator);
		}

		if ($excluded_articles = $params->get('excluded_articles', ''))
		{
			$excluded_articles = explode("\r\n", $excluded_articles);
			$query->whereRaw('id', 'NOT IN(' . implode(',', $excluded_articles) . ')');
		}

		$date_filtering = $params->get('date_filtering', 'off');
		if ($date_filtering !== 'off')
		{
			$fld = str_replace('a.', '', $params->get('date_field', 'a.created'));

			$query->where($fld, '>=', $params->get('start_date_range', '1000-01-01 00:00:00'));
			$query->where($fld, '<', $params->get('end_date_range', '9999-12-31 23:59:59'));
			if ($relativeDate = $params->get('relative_date', 30))
			{
				$query->whereRaw($fld, '>= DATE_SUB(' . Date::toSql() . ', INTERVAL ' . $relativeDate . ' DAY)');
			}
		}

		// Filter by language
		$query->whereEquals('language', App::get('language.filter'));

		$query->start(0)
			->limit((int) $params->get('count', 5));

		$items = $query->rows();

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

		$access = !Component::params('com_content')->get('show_noauth');

		// Prepare data for display using display options
		foreach ($items as $item)
		{
			$item->slug    = $item->id . ':' . $item->alias;
			$item->catslug = $item->catid ? $item->catid . ':' . $item->category_alias : $item->catid;

			if ($access || in_array($item->access, User::getAuthorisedViewLevels()))
			{
				// We know that user has the privilege to view the article
				$item->link = Route::url(\Components\Content\Site\Helpers\Route::getArticleRoute($item->slug, $item->catslug, $item->language));
			}
			else
			{
				// Angie Fixed Routing
				$menu = \App::get('menu');
				$menuitems = $menu->getItems('link', 'index.php?option=com_login');
				if (isset($menuitems[0]))
				{
					$Itemid = $menuitems[0]->id;
				}
				elseif (Request::getInt('Itemid') > 0)
				{
					// Use Itemid from requesting page only if there is no existing menu
					$Itemid = Request::getInt('Itemid');
				}

				$item->link = Route::url('index.php?option=com_users&view=loginItemid=' . $Itemid);
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
				$item->displayCategoryLink  = Route::url(\Components\Content\Site\Helpers\Route::getCategoryRoute($item->catid));
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
		$ptString = Str::truncate($html, $maxLength, array('html' => true));

		for ($maxLength; $maxLength < $baseLength;)
		{
			// Now get the string if we allow html.
			$htmlString = Str::truncate($html, $maxLength, array('html' => true));

			// Now get the plain text from the html string.
			$htmlStringToPtString = Str::truncate($htmlString, $maxLength);

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
