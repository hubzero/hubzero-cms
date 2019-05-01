<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Content\Models\Article;
use Components\Content\Site\Helpers\Query as HelperQuery;
use Components\Content\Site\Helpers\Route as HelperRoute;
use stdClass;
use Document;
use Pathway;
use Request;
use Config;
use Event;
use User;
use Lang;
use Html;
use App;

/**
 * Content articles controller
 */
class Articles extends SiteController
{
	/**
	 * Display a single entry
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$params = App::get('menu.params');

		$filters = array();
		$filters['id'] = Request::getInt('id');
		$filters['language'] = Lang::isMultilang();
		$filters['access'] = null;
		$filters['context'] = 'com_content.article';

		// Filter by published state.
		if (!User::authorise('core.edit.state', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			$filters['state'] = array(Article::STATE_PUBLISHED, Article::STATE_ARCHIVED);
		}

		$data = Article::oneByFilters($filters);

		if (!$data || !$data->get('id'))
		{
			App::abort(404, Lang::txt('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
		}

		// Check for published state if filter set.
		if (isset($filters['state']) && !in_array($data->get('state'), $filters['state']))
		{
			App::abort(404, Lang::txt('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
		}

		$item = $data->toObject();

		$registry = $data->attribs;

		$item->params = clone $params;
		$item->params->merge($registry);

		$item->metadata = $data->metadata;

		// Technically guest could edit an article, but lets not check that to improve performance a little.
		if (!User::isGuest())
		{
			$userId = User::get('id');
			$asset  = 'com_content.article.' . $item->id;

			// Check general edit permission first.
			if (User::authorise('core.edit', $asset))
			{
				$item->params->set('access-edit', true);
			}
			// Now check if edit.own is available.
			elseif (!empty($userId) && User::authorise('core.edit.own', $asset))
			{
				// Check for a valid user and that they are the owner.
				if ($userId == $data->created_by)
				{
					$item->params->set('access-edit', true);
				}
			}
		}

		// Compute view access permissions.
		if ($access = $filters['access'])
		{
			// If the access filter has been set, we already know this user can view.
			$item->params->set('access-view', true);
		}
		else
		{
			// If no access filter is set, the layout takes some responsibility for display of limited information.
			$groups = User::getAuthorisedViewLevels();

			if ($item->catid == 0 || $item->category_access === null)
			{
				$item->params->set('access-view', in_array($item->access, $groups));
			}
			else
			{
				$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
			}
		}

		// Add router helpers.
		$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
		$item->catslug = $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
		$item->parent_slug = $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;

		// TODO: Change based on shownoauth
		$item->readmore_link = Route::url(HelperRoute::getArticleRoute($item->slug, $item->catslug, $item->language));

		$this->view->setLayout('default');

		// Merge article params. If this is single-article view, menu params override article params
		// Otherwise, article params override menu item params
		$active = App::get('menu')->getActive();
		$temp   = clone ($params);

		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;
			// If the current view is the active item and an article view for this article, then the menu item params take priority
			if (strpos($currentLink, 'view=article') && (strpos($currentLink, '&id=' . (string) $item->id)))
			{
				// $item->params are the article params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);

				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->view->setLayout($active->query['layout']);
				}
			}
			else
			{
				// Current view is not a single article, so the article params take priority here
				// Merge the menu item params with the article params so that the article params take priority
				$temp->merge($item->params);
				$item->params = $temp;

				// Check for alternative layouts (since we are not in a single-article menu item)
				// Single-article menu item layout takes priority over alt layout for an article
				if ($layout = $item->params->get('article_layout'))
				{
					$this->view->setLayout($layout);
				}
			}
		}
		else
		{
			// Merge so that article params take priority
			$temp->merge($item->params);
			$item->params = $temp;
			// Check for alternative layouts (since we are not in a single-article menu item)
			// Single-article menu item layout takes priority over alt layout for an article
			if ($layout = $item->params->get('article_layout'))
			{
				$this->view->setLayout($layout);
			}
		}

		$offset = Request::getUInt('limitstart');

		// Check the view access to the article (the model has already computed the values).
		if ($item->params->get('access-view') == false && ($item->params->get('show_noauth', '0') == '0'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		if ($item->params->get('show_intro', '1') == '1')
		{
			$item->text = $item->introtext . ' ' . $item->fulltext;
		}
		elseif ($item->fulltext)
		{
			$item->text = $item->fulltext;
		}
		else
		{
			$item->text = $item->introtext;
		}

		//
		// Process the content plugins.
		//
		$results = Event::trigger('content.onContentPrepare', array ('com_content.article', &$item, &$params, $offset));

		$item->event = new stdClass();
		$results = Event::trigger('content.onContentAfterTitle', array('com_content.article', &$item, &$params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = Event::trigger('content.onContentBeforeDisplay', array('com_content.article', &$item, &$params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = Event::trigger('content.onContentAfterDisplay', array('com_content.article', &$item, &$params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		// Increment the hit counter of the article.
		if (!$params->get('intro_only') && $offset == 0)
		{
			// [!] HUBZERO - (zooley) Removing hit counter as it can contribute to performance issues. Need a better way of doing this.
			//$data->hit();
		}

		// Escape strings for HTML output
		$pageclass_sfx = htmlspecialchars($item->params->get('pageclass_sfx'));
		$print = Request::getBool('print');

		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = App::get('menu')->getActive();
		if ($menu)
		{
			$params->def('page_heading', $params->get('page_title', $menu->title));
		}
		else
		{
			$params->def('page_heading', Lang::txt('JGLOBAL_ARTICLES'));
		}

		$title = $params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this article
		if ($menu && ($menu->query['option'] != 'com_content' || $menu->query['view'] != 'article' || $id != $item->id))
		{
			// If this is not a single article menu item, set the page title to the article title
			if ($item->title)
			{
				$title = $item->title;
			}
			$path = array(array(
				'title' => $item->title,
				'link' => ''
			));
			$category = \Components\Categories\Helpers\Categories::getInstance('Content')->get($item->catid);
			while ($category && ($menu->query['option'] != 'com_content' || $menu->query['view'] == 'article' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array(
					'title' => $category->title,
					'link' => HelperRoute::getCategoryRoute($category->id)
				);
				$category = $category->getParent();
			}
			$path = array_reverse($path);
			foreach ($path as $itm)
			{
				Pathway::append($itm['title'], $itm['link']);
			}
		}

		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = Config::get('sitename');
		}
		elseif (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}
		if (empty($title))
		{
			$title = $item->title;
		}
		Document::setTitle($title);

		if ($item->metadesc)
		{
			Document::setDescription($item->metadesc);
		}
		elseif (!$item->metadesc && $params->get('menu-meta_description'))
		{
			Document::setDescription($params->get('menu-meta_description'));
		}

		if ($item->metakey)
		{
			Document::setMetadata('keywords', $item->metakey);
		}
		elseif (!$item->metakey && $params->get('menu-meta_keywords'))
		{
			Document::setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			Document::setMetadata('robots', $params->get('robots'));
		}

		if (Config::get('MetaAuthor') == '1')
		{
			Document::setMetaData('author', $item->author);
		}

		$mdata = $item->metadata->toArray();
		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				Document::setMetadata($k, $v);
			}
		}

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($item->page_title))
		{
			$item->title = $item->title . ' - ' . $item->page_title;
			Document::setTitle($item->page_title . ' - ' . Lang::txt('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $offset + 1));
		}

		if (Request::getBool('print'))
		{
			Document::setMetaData('robots', 'noindex, nofollow');
		}

		$this->view
			->setName('article')
			->set('item', $item)
			->set('pageclass_sfx', $pageclass_sfx)
			->set('params', $params)
			->set('print', $print)
			->display();
	}

	/**
	 * Display a list of archived entries
	 *
	 * @return  void
	 */
	public function archiveTask()
	{
		$params = App::get('menu.params');

		// List state information
		$filters = array();
		$filters['limit'] = Request::getUInt('limit', Config::get('list_limit', 0));
		$filters['start'] = Request::getUInt('limitstart', 0);
		$filters['context'] = 'com_content.archive';

		$orderCol = Request::getCmd('filter_order', 'a.ordering');
		$filter_fields = array(
			'id', 'a.id',
			'title', 'a.title',
			'alias', 'a.alias',
			'checked_out', 'a.checked_out',
			'checked_out_time', 'a.checked_out_time',
			'catid', 'a.catid', 'category_title',
			'state', 'a.state',
			'access', 'a.access', 'access_level',
			'created', 'a.created',
			'created_by', 'a.created_by',
			'ordering', 'a.ordering',
			'featured', 'a.featured',
			'language', 'a.language',
			'hits', 'a.hits',
			'publish_up', 'a.publish_up',
			'publish_down', 'a.publish_down',
			'images', 'a.images',
			'urls', 'a.urls',
		);
		if (!in_array($orderCol, $filter_fields))
		{
			$orderCol = 'a.ordering';
		}
		$filters['ordering'] = $orderCol;

		$listOrder = Request::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}
		$filters['direction'] = $listOrder;

		$filters['published'] = Article::STATE_ARCHIVED;
		/*if (!User::authorise('core.edit.state', 'com_content')
		 && !User::authorise('core.edit', 'com_content'))
		{
			// filter on published for those who do not have edit or edit.state rights.
			$filters['published'] = Article::STATE_ARCHIVED;
		}*/

		$filters['language'] = App::get('language.filter');

		// process show_noauth parameter
		if (!$params->get('show_noauth'))
		{
			$filters['access'] = true;
		}
		else
		{
			$filters['access'] = false;
		}

		$filters['layout'] = Request::getCmd('layout');

		$itemid = Request::getInt('Itemid', 0);
		$limit  = Request::getState('com_content.archive.list' . $itemid . '.limit', 'limit', $params->get('display_num'), 'uint');
		$filters['month']  = Request::getInt('month');
		$filters['year']   = Request::getInt('year');

		$query = Article::allByFilters($filters);

		// No category ordering
		$categoryOrderby = '';
		$articleOrderby = $params->get('orderby_sec', 'rdate');
		$articleOrderDate = $params->get('order_date');

		$secondary = HelperQuery::orderbySecondary($articleOrderby, $articleOrderDate);
		$primary   = HelperQuery::orderbyPrimary($categoryOrderby);

		if ($primary)
		{
			$query->order($primary[0], $primary[1]);
		}
		if ($secondary)
		{
			$query->order($secondary[0], $secondary[1]);
		}
		$query->order('a.created', 'desc');

		// Add routing for archive
		//sqlsrv changes
		$case_when = ' CASE WHEN ';
		$case_when .= 'CHAR_LENGTH(a.alias)';
		$case_when .= ' THEN ';
		//$a_id = $query->castAsChar('a.id');
		$case_when .= 'CONCAT_WS(\':\', ' . implode(', ', array('a.id', 'a.alias')) . ')'; //$query->concatenate(array($a_id, 'a.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= 'a.id END';

		$query->select($case_when, 'slug');

		$case_when = ' CASE WHEN ';
		$case_when .= 'CHAR_LENGTH(c.alias)';
		$case_when .= ' THEN ';
		//$c_id = $query->castAsChar('c.id');
		$case_when .= 'CONCAT_WS(\':\', ' . implode(', ', array('c.id', 'c.alias')) . ')'; //$query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= 'c.id END';
		$query->select($case_when, 'catslug');

		// Filter on month, year
		// First, get the date field
		$queryDate = HelperQuery::getQueryDate($articleOrderDate);

		if ($month = $filters['month'])
		{
			$query->whereEquals('MONTH(' . $queryDate . ')', $month);
		}

		if ($year = $filters['year'])
		{
			$query->whereEquals('YEAR(' . $queryDate . ')', $year);
		}

		$total = with(clone $query)->total();

		$items = $query
			->start($filters['start'])
			->limit($filters['limit'])
			->rows()
			->toObject();

		$pagination = new \Hubzero\Pagination\Paginator($total, $filters['start'], $filters['limit']);

		foreach ($items as $item)
		{
			$articleParams = new \Hubzero\Config\Registry($item->attribs);

			// Unpack readmore and layout params
			$item->alternative_readmore = $articleParams->get('alternative_readmore');
			$item->layout = $articleParams->get('layout');

			$item->params = clone $params;

			// For blogs, article params override menu item params only if menu param = 'use_article'
			// Otherwise, menu item params control the layout
			// If menu item is 'use_article' and there is no article param, use global
			if (Request::getString('layout') == 'blog'
			 || Request::getString('view') == 'featured'
			 || $params->get('layout_type') == 'blog')
			{
				// create an array of just the params set to 'use_article'
				$menuParamsArray = $params->toArray();
				$articleArray = array();

				foreach ($menuParamsArray as $key => $value)
				{
					if ($value === 'use_article')
					{
						// if the article has a value, use it
						if ($articleParams->get($key) != '')
						{
							// get the value from the article
							$articleArray[$key] = $articleParams->get($key);
						}
						else
						{
							// otherwise, use the global value
							$articleArray[$key] = $globalParams->get($key);
						}
					}
				}

				// merge the selected article params
				if (count($articleArray) > 0)
				{
					$articleParams = new \Hubzero\Config\Registry($articleArray);
					$item->params->merge($articleParams);
				}
			}
			else
			{
				// For non-blog layouts, merge all of the article params
				$item->params->merge($articleParams);
			}

			// get display date
			switch ($item->params->get('list_show_date'))
			{
				case 'modified':
					$item->displayDate = $item->modified;
					break;

				case 'published':
					$item->displayDate = ($item->publish_up == 0) ? $item->created : $item->publish_up;
					break;

				default:
				case 'created':
					$item->displayDate = $item->created;
					break;
			}

			// Compute the asset access permissions.
			// Technically guest could edit an article, but lets not check that to improve performance a little.
			if (!User::isGuest())
			{
				$asset = 'com_content.article.' . $item->id;

				// Check general edit permission first.
				if (User::authorise('core.edit', $asset))
				{
					$item->params->set('access-edit', true);
				}
				// Now check if edit.own is available.
				elseif (!empty($userId) && User::authorise('core.edit.own', $asset))
				{
					// Check for a valid user and that they are the owner.
					if ($userId == $item->created_by)
					{
						$item->params->set('access-edit', true);
					}
				}
			}

			$access = $filters['access'];

			if ($access)
			{
				// If the access filter has been set, we already have only the articles this user can view.
				$item->params->set('access-view', true);
			}
			else
			{
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				if ($item->catid == 0 || $item->category_access === null)
				{
					$item->params->set('access-view', in_array($item->access, $groups));
				}
				else
				{
					$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}

			$item->catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
		}

		$form = new stdClass();

		// Month Field
		$months = array(
			''   => Lang::txt('COM_CONTENT_MONTH'),
			'01' => Lang::txt('JANUARY_SHORT'),
			'02' => Lang::txt('FEBRUARY_SHORT'),
			'03' => Lang::txt('MARCH_SHORT'),
			'04' => Lang::txt('APRIL_SHORT'),
			'05' => Lang::txt('MAY_SHORT'),
			'06' => Lang::txt('JUNE_SHORT'),
			'07' => Lang::txt('JULY_SHORT'),
			'08' => Lang::txt('AUGUST_SHORT'),
			'09' => Lang::txt('SEPTEMBER_SHORT'),
			'10' => Lang::txt('OCTOBER_SHORT'),
			'11' => Lang::txt('NOVEMBER_SHORT'),
			'12' => Lang::txt('DECEMBER_SHORT')
		);
		$form->monthField = Html::select(
			'genericlist',
			$months,
			'month',
			array(
				'list.attr'   => 'size="1" class="inputbox"',
				'list.select' => $month,
				'option.key'  => null
			)
		);

		// Year Field
		$years = array();
		$years[] = Html::select('option', null, Lang::txt('JYEAR'));
		for ($i = 2000; $i <= 2020; $i++)
		{
			$years[] = Html::select('option', $i, $i);
		}
		$form->yearField = Html::select(
			'genericlist',
			$years,
			'year',
			array(
				'list.attr' => 'size="1" class="inputbox"',
				'list.select' => $year
			)
		);

		$form->limitField = ''; //$pagination->getLimitBox();

		//Escape strings for HTML output
		$pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = App::get('menu')->getActive();
		if ($menu)
		{
			$params->def('page_heading', $params->get('page_title', $menu->title));
		}
		else
		{
			$params->def('page_heading', Lang::txt('JGLOBAL_ARTICLES'));
		}

		$title = $params->get('page_title', '');
		if (empty($title))
		{
			$title = Config::get('sitename');
		}
		elseif (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}
		Document::setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			Document::setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			Document::setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			Document::setMetadata('robots', $params->get('robots'));
		}

		$this->view
			->setName('archive')
			->setLayout('default')
			->set('form', $form)
			->set('items', $items)
			->set('pageclass_sfx', $pageclass_sfx)
			->set('params', $params)
			->set('user', User::getInstance())
			->set('pagination', $pagination)
			->display();
	}

	/**
	 * Display a list of featured entries
	 *
	 * @return  void
	 */
	public function featuredTask()
	{
		$params = App::get('menu.params');

		$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');

		$filters = array();
		$filters['start'] = Request::getInt('limitstart', 0);
		$filters['limit'] = $limit;
		$filters['links'] = $params->get('num_links');
		$filters['frontpage'] = true;
		$filters['context'] = 'com_content.featured';

		if (!User::authorise('core.edit.state', 'com_content')
		 && !User::authorise('core.edit', 'com_content'))
		{
			// filter on published for those who do not have edit or edit.state rights.
			$filters['published'] = array(Article::STATE_PUBLISHED);
		}
		else
		{
			$filters['published'] = array(Article::STATE_UNPUBLISHED, Article::STATE_PUBLISHED, Article::STATE_ARCHIVED);
		}

		// process show_noauth parameter
		if (!$params->get('show_noauth'))
		{
			$filters['access'] = User::getAuthorisedViewLevels();
		}
		else
		{
			$filters['access'] = false;
		}

		// check for category selection
		if ($params->get('featured_categories') && implode(',', $params->get('featured_categories')) == true)
		{
			$filters['frontpage.categories'] = $params->get('featured_categories');
		}

		$query = Article::allByFilters($filters);

		// Filter by frontpage.
		if ($filters['frontpage'])
		{
			$query->join('#__content_frontpage AS fp', 'fp.content_id', 'a.id', 'inner');
		}

		// Filter by categories
		if (is_array($featuredCategories = $filters['frontpage.categories']))
		{
			$query->whereIn('a.catid', $featuredCategories);
		}

		$articleOrderby   = $params->get('orderby_sec', 'rdate');
		$articleOrderDate = $params->get('order_date');
		$categoryOrderby  = $params->def('orderby_pri', '');
		$secondary = HelperQuery::orderbySecondary($articleOrderby, $articleOrderDate);
		$primary   = HelperQuery::orderbyPrimary($categoryOrderby);

		if ($primary)
		{
			$query->order($primary[0], $primary[1]);
		}
		if ($secondary)
		{
			$query->order($secondary[0], $secondary[1]);
		}
		$query->order('a.created', 'DESC');

		$total = with(clone $query)->total();

		$items = $query
			->start($filters['start'])
			->limit($limit)
			->rows()
			->toObject();

		$pagination = new \Hubzero\Pagination\Paginator($total, $filters['start'], $filters['limit']);

		// PREPARE THE DATA

		// Get the metrics for the structural page layout.
		$numLeading = $params->def('num_leading_articles', 1);
		$numIntro   = $params->def('num_intro_articles', 4);
		$numLinks   = $params->def('num_links', 4);

		// Compute the article slugs and prepare introtext (runs content plugins).
		foreach ($items as $i => & $item)
		{
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
			$item->catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

			// No link for ROOT category
			if ($item->parent_alias == 'root')
			{
				$item->parent_slug = null;
			}

			$item->event = new stdClass();

			// Old plugins: Ensure that text property is available
			if (!isset($item->text))
			{
				$item->text = $item->introtext;
			}

			$results = Event::trigger('content.onContentPrepare', array('com_content.featured', &$item, &$params, 0));

			// Old plugins: Use processed text as introtext
			$item->introtext = $item->text;

			$results = Event::trigger('content.onContentAfterTitle', array('com_content.featured', &$item, &$item->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results = Event::trigger('content.onContentBeforeDisplay', array('com_content.featured', &$item, &$item->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results = Event::trigger('content.onContentAfterDisplay', array('com_content.featured', &$item, &$item->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		$lead_items = array();
		$intro_items = array();
		$link_items = array();
		$columns = 1;

		// Preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interogate the arrays.
		$max = count($items);

		// The first group is the leading articles.
		$limit = $numLeading;
		for ($i = 0; $i < $limit && $i < $max; $i++)
		{
			$lead_items[$i] = &$items[$i];
		}

		// The second group is the intro articles.
		$limit = $numLeading + $numIntro;
		// Order articles across, then down (or single column mode)
		for ($i = $numLeading; $i < $limit && $i < $max; $i++)
		{
			$intro_items[$i] = &$items[$i];
		}

		$this->columns = max(1, $params->def('num_columns', 1));
		$order = $params->def('multi_column_order', 1);

		if ($order == 0 && $this->columns > 1)
		{
			// call order down helper
			$intro_items = HelperQuery::orderDownColumns($intro_items, $columns);
		}

		// The remainder are the links.
		for ($i = $numLeading + $numIntro; $i < $max; $i++)
		{
			$link_items[$i] = &$items[$i];
		}

		//Escape strings for HTML output
		$pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = App::get('menu')->getActive();
		if ($menu)
		{
			$params->def('page_heading', $params->get('page_title', $menu->title));
		}
		else
		{
			$params->def('page_heading', Lang::txt('JGLOBAL_ARTICLES'));
		}

		$title = $params->get('page_title', '');
		if (empty($title))
		{
			$title = Config::get('sitename');
		}
		elseif (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}
		Document::setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			Document::setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			Document::setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			Document::setMetadata('robots', $params->get('robots'));
		}

		// Add feed links
		if ($params->get('show_feed_link', 1))
		{
			$link = '&format=feed&limitstart=';

			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			Document::addHeadLink(Route::url($link . '&type=rss'), 'alternate', 'rel', $attribs);

			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			Document::addHeadLink(Route::url($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}

		$this->view
			->setName('featured')
			->setLayout('default')
			->set('params', $params)
			->set('items', $items)
			->set('pagination', $pagination)
			->set('pageclass_sfx', $pageclass_sfx)
			->set('lead_items', $lead_items)
			->set('intro_items', $intro_items)
			->set('link_items', $link_items)
			->set('columns', $columns)
			->set('user', User::getInstance())
			->display();
	}

	/**
	 * Display a list of categories
	 *
	 * @return  void
	 */
	public function categoriesTask()
	{
		$params = App::get('menu.params');

		// List state information
		$filters = array();
		$filters['parentId']  = Request::getInt('id');
		$filters['published'] = Article::STATE_PUBLISHED;
		$filters['access']    = true;
		$filters['context']   = 'com_content.categories';
		$recursive = false;

		$active = App::get('menu')->getActive();
		$mparams = new \Hubzero\Config\Registry();

		if ($active)
		{
			$mparams = $active->params;
		}

		$options = array();
		$options['countItems'] = $mparams->get('show_cat_num_articles_cat', 1) || !$mparams->get('show_empty_categories_cat', 0);

		$categories = \Components\Categories\Helpers\Categories::getInstance('Content', $options);
		$parent = $categories->get($filters['parentId'], 'root');

		if ($parent == false)
		{
			App::abort(404, Lang::txt('COM_CONTENT_ERROR_PARENT_CATEGORY_NOT_FOUND'));
		}

		$items = $parent->getChildren($recursive);

		if ($items === false)
		{
			App::abort(404, Lang::txt('COM_CONTENT_ERROR_CATEGORY_NOT_FOUND'));
		}

		$items = array($parent->id => $items);

		//Escape strings for HTML output
		$pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$maxLevelcat = $params->get('maxLevelcat', -1);

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = App::get('menu')->getActive();
		if ($menu)
		{
			$params->def('page_heading', $params->get('page_title', $menu->title));
		}
		else
		{
			$params->def('page_heading', Lang::txt('JGLOBAL_ARTICLES'));
		}

		$title = $params->get('page_title', '');
		if (empty($title))
		{
			$title = Config::get('sitename');
		}
		elseif (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}
		Document::setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			Document::setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			Document::setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			Document::setMetadata('robots', $params->get('robots'));
		}

		$this->view
			->setName('categories')
			->setLayout('default')
			->set('params', $params)
			->set('items', $items)
			->set('parent', $parent)
			//->set('pagination', $pagination)
			->set('pageclass_sfx', $pageclass_sfx)
			->set('maxLevelcat', $maxLevelcat)
			->set('user', User::getInstance())
			->display();
	}

	/**
	 * Display a list of articles for a category
	 *
	 * @return  void
	 */
	public function categoryTask()
	{
		$this->view
			->setName('category')
			->setLayout(Request::getCmd('layout', 'default'));

		$globalparams = App::get('menu.params');
		$menuParams = new \Hubzero\Config\Registry;
		if ($menu = App::get('menu')->getActive())
		{
			$menuParams->parse($menu->params);
		}
		$params = clone $menuParams;
		$params->merge($globalparams);

		$filters = array();
		$filters['category_id'] = Request::getInt('id');
		$filters['start']       = Request::getInt('limitstart', 0);
		$filters['params']      = $params;
		$filters['context']     = 'com_content.category';
		$filters['frontpage'] = false;
		$filters['frontpage.categories'] = null;

		if (!User::authorise('core.edit.state', 'com_content')
		 && !User::authorise('core.edit', 'com_content'))
		{
			// filter on published for those who do not have edit or edit.state rights.
			$filters['published'] = array(Article::STATE_PUBLISHED);
		}
		else
		{
			$filters['published'] = array(Article::STATE_UNPUBLISHED, Article::STATE_PUBLISHED, Article::STATE_ARCHIVED);
		}

		// process show_noauth parameter
		if (!$params->get('show_noauth'))
		{
			$filters['access'] = User::getAuthorisedViewLevels();
		}
		else
		{
			$filters['access'] = false;
		}

		// Optional filter text
		$filters['filter'] = Request::getString('filter-search');

		// filter.order
		$itemid = Request::getInt('id', 0) . ':' . Request::getInt('Itemid', 0);
		$orderCol = Request::getState('com_content.category.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
		$filter_fields = array(
			'id', 'a.id',
			'title', 'a.title',
			'alias', 'a.alias',
			'checked_out', 'a.checked_out',
			'checked_out_time', 'a.checked_out_time',
			'catid', 'a.catid', 'category_title',
			'state', 'a.state',
			'access', 'a.access', 'access_level',
			'created', 'a.created',
			'created_by', 'a.created_by',
			'modified', 'a.modified',
			'ordering', 'a.ordering',
			'featured', 'a.featured',
			'language', 'a.language',
			'hits', 'a.hits',
			'publish_up', 'a.publish_up',
			'publish_down', 'a.publish_down',
			'author', 'a.author'
		);
		if (!in_array($orderCol, $filter_fields))
		{
			$orderCol = 'a.ordering';
		}
		$filters['ordering'] = $orderCol;

		$listOrder = Request::getState('com_content.category.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}
		$filters['direction'] = $listOrder;

		// set limit for query. If list, use parameter. If blog, add blog parameters for limit.
		if (Request::getCmd('layout') == 'blog' || $params->get('layout_type') == 'blog')
		{
			$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');
			$filters['links'] = $params->get('num_links');
		}
		else
		{
			$limit = Request::getState('com_content.category.list.' . $itemid . '.limit', 'limit', $params->get('display_num'), 'uint');
		}

		$filters['limit'] = $limit;

		// set the depth of the category query based on parameter
		$showSubcategories = $params->get('show_subcategory_content', '0');

		if ($showSubcategories)
		{
			$filters['max_category_levels'] = $params->get('show_subcategory_content', '1');
			$filters['subcategories'] = true;
		}

		$filters['language'] = App::get('language.filter');

		// Get category
		$options = array();
		$options['countItems'] = $params->get('show_cat_num_articles', 1) || !$params->get('show_empty_categories_cat', 0);

		$categories = \Components\Categories\Helpers\Categories::getInstance('Content', $options);
		$category = $categories->get($filters['category_id'], 'root');

		if ($category == false)
		{
			App::abort(404, Lang::txt('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		// Check general create permission.
		if (User::authorise('core.create', 'com_content.category.' . $category->id))
		{
			$category->getParams()->set('access-create', true);
		}

		// TODO: Why aren't we lazy loading the children and siblings?
		$children = $category->getChildren();
		if (count($children))
		{
			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha')
			{
				\Hubzero\Utility\Arr::sortObjects($children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}
		$parent = false;

		if ($category->getParent())
		{
			$parent = $category->getParent();
		}

		$rightsibling = $category->getSibling();
		$leftsibling = $category->getSibling(false);

		if ($parent == false)
		{
			App::abort(404, Lang::txt('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		// Setup the category parameters.
		$cparams = $category->getParams();
		$category->params = clone($params);
		$category->params->merge($cparams);

		// Check whether category access level allows access.
		$groups = User::getAuthorisedViewLevels();
		if (!in_array($category->access, $groups))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// PREPARE THE DATA
		// Get the metrics for the structural page layout.
		$numLeading = $params->def('num_leading_articles', 1);
		$numIntro   = $params->def('num_intro_articles', 4);
		$numLinks   = $params->def('num_links', 4);

		$query = Article::allByFilters($filters);

		// Filter by frontpage.
		if ($filters['frontpage'])
		{
			$query->join('#__content_frontpage AS fp', 'fp.content_id', 'a.id', 'inner');
		}

		// Filter by categories
		if (is_array($featuredCategories = $filters['frontpage.categories']))
		{
			$query->whereIn('a.catid', $featuredCategories);
		}

		$articleOrderby   = $params->get('orderby_sec', 'rdate');
		$articleOrderDate = $params->get('order_date');
		$categoryOrderby  = $params->def('orderby_pri', '');
		$secondary = HelperQuery::orderbySecondary($articleOrderby, $articleOrderDate);
		$primary   = HelperQuery::orderbyPrimary($categoryOrderby);

		if ($primary)
		{
			$query->order($primary[0], $primary[1]);
		}
		if ($secondary)
		{
			$query->order($secondary[0], $secondary[1]);
		}
		$query->order('a.created', 'DESC');

		$total = with(clone $query)->total();

		$items = $query
			->start($filters['start'])
			->limit($limit)
			->rows()
			->toObject();

		$pagination = new \Hubzero\Pagination\Paginator($total, $filters['start'], $filters['limit']);

		// Compute the article slugs and prepare introtext (runs content plugins).
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item = &$items[$i];
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

			// No link for ROOT category
			if ($item->parent_alias == 'root')
			{
				$item->parent_slug = null;
			}

			$item->catslug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
			$item->event = new stdClass();

			// Old plugins: Ensure that text property is available
			if (!isset($item->text))
			{
				$item->text = $item->introtext;
			}

			$item->params = new \Hubzero\Config\Registry($item->attribs);
			$item->metadata = new \Hubzero\Config\Registry($item->metadata);

			$results = Event::trigger('content.onContentPrepare', array ('com_content.category', &$item, &$item->params, 0));

			// Old plugins: Use processed text as introtext
			$item->introtext = $item->text;

			$results = Event::trigger('content.onContentAfterTitle', array('com_content.category', &$item, &$item->params, 0));
			$item->event->afterDisplayTitle = trim(implode("\n", $results));

			$results = Event::trigger('content.onContentBeforeDisplay', array('com_content.category', &$item, &$item->params, 0));
			$item->event->beforeDisplayContent = trim(implode("\n", $results));

			$results = Event::trigger('content.onContentAfterDisplay', array('com_content.category', &$item, &$item->params, 0));
			$item->event->afterDisplayContent = trim(implode("\n", $results));
		}

		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		$menu = App::get('menu')->getActive();

		if ((!$menu) || ((strpos($menu->link, 'view=category') === false) || (strpos($menu->link, '&id=' . (string) $category->id) === false)))
		{
			// Get the layout from the merged category params
			if ($layout = $category->params->get('category_layout'))
			{
				$this->view->setLayout($layout);
			}
		}
		// At this point, we are in a menu item, so we don't override the layout
		elseif (isset($menu->query['layout']))
		{
			// We need to set the layout from the query in case this is an alternative menu item (with an alternative layout)
			$this->view->setLayout($menu->query['layout']);
		}

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = App::get('menu')->getActive();

		if ($menu)
		{
			$params->def('page_heading', $params->get('page_title', $menu->title));
		}
		else
		{
			$params->def('page_heading', Lang::txt('JGLOBAL_ARTICLES'));
		}

		$id = (int) @$menu->query['id'];

		if ($menu && ($menu->query['option'] != 'com_content' || $menu->query['view'] == 'article' || $id != $category->id))
		{
			$path = array(array(
				'title' => $category->title,
				'link'  => ''
			));
			$parent = $category->getParent();

			while (($menu->query['option'] != 'com_content' || $menu->query['view'] == 'article' || $id != $parent->id) && $parent->id > 1)
			{
				$path[] = array(
					'title' => $parent->title,
					'link'  => HelperRoute::getCategoryRoute($parent->id)
				);
				$parent = $category->getParent();
			}

			$path = array_reverse($path);

			foreach ($path as $crumb)
			{
				Pathway::append($crumb['title'], $crumb['link']);
			}
		}

		$lead_items = array();
		$intro_items = array();
		$link_items = array();
		$columns = 1;

		// For blog layouts, preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interrogate the arrays.
		if ($params->get('layout_type') == 'blog' || $this->view->getLayout() == 'blog')
		{
			$max = count($items);

			// The first group is the leading articles.
			$limit = $numLeading;
			for ($i = 0; $i < $limit && $i < $max; $i++)
			{
				$lead_items[$i] = &$items[$i];
			}

			// The second group is the intro articles.
			$limit = $numLeading + $numIntro;
			// Order articles across, then down (or single column mode)
			for ($i = $numLeading; $i < $limit && $i < $max; $i++)
			{
				$intro_items[$i] = &$items[$i];
			}

			$this->columns = max(1, $params->def('num_columns', 1));
			$order = $params->def('multi_column_order', 1);

			if ($order == 0 && $this->columns > 1)
			{
				// call order down helper
				$this->intro_items = HelperQuery::orderDownColumns($this->intro_items, $this->columns);
			}

			$limit = $numLeading + $numIntro + $numLinks;

			// The remainder are the links.
			for ($i = $numLeading + $numIntro; $i < $limit && $i < $max; $i++)
			{
				$link_items[$i] = &$items[$i];
			}
		}

		$children = array($category->id => $children);

		// Escape strings for HTML output
		$pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$maxLevel = $params->get('maxLevel', -1);

		$title = $params->get('page_title', '');

		if (empty($title))
		{
			$title = Config::get('sitename');
		}
		elseif (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}

		Document::setTitle($title);

		if ($category->metadesc)
		{
			Document::setDescription($category->metadesc);
		}
		elseif (!$category->metadesc && $params->get('menu-meta_description'))
		{
			Document::setDescription($params->get('menu-meta_description'));
		}

		if ($category->metakey)
		{
			Document::setMetadata('keywords', $category->metakey);
		}
		elseif (!$category->metakey && $params->get('menu-meta_keywords'))
		{
			Document::setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			Document::setMetadata('robots', $params->get('robots'));
		}

		if (Config::get('MetaAuthor') == '1')
		{
			Document::setMetaData('author', $category->getMetadata()->get('author'));
		}

		$mdata = $category->getMetadata()->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				Document::setMetadata($k, $v);
			}
		}

		// Add feed links
		if ($params->get('show_feed_link', 1))
		{
			$link = '&format=feed&limitstart=';

			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			Document::addHeadLink(Route::url($link . '&type=rss'), 'alternate', 'rel', $attribs);

			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			Document::addHeadLink(Route::url($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}

		$this->view
			->set('params', $params)
			->set('items', $items)
			->set('pagination', $pagination)
			->set('pageclass_sfx', $pageclass_sfx)
			->set('maxLevel', $maxLevel)
			->set('parent', $parent)
			->set('category', $category)
			->set('children', $children)
			->set('filters', $filters)
			->set('lead_items', $lead_items)
			->set('intro_items', $intro_items)
			->set('link_items', $link_items)
			->set('columns', $columns)
			->set('user', User::getInstance())
			->display();
	}

	/**
	 * Display an edit form
	 *
	 * @return  void
	 */
	public function editTask()
	{
		$params = App::get('menu.params');

		$id = Request::getInt('a_id');
		$catId = Request::getInt('catid');
		$return_page = urldecode(base64_decode(Request::getString('return', null)));

		$userId = User::get('id');
		$asset  = 'com_content.article.' . $id;

		$item = Article::oneOrFail($id);

		if ($data = User::getState($asset . '.data'))
		{
			$item->set($data);

			User::setState($asset . '.data', null);
		}

		$item->params = $item->attribs;

		// Compute selected asset permissions.
		// Check general edit permission first.
		if (User::authorise('core.edit', $asset))
		{
			$item->params->set('access-edit', true);
		}
		// Now check if edit.own is available.
		elseif (!empty($userId) && User::authorise('core.edit.own', $asset))
		{
			// Check for a valid user and that they are the owner.
			if ($userId == $item->created_by)
			{
				$item->params->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($id)
		{
			// Existing item
			$item->params->set('access-change', User::authorise('core.edit.state', $asset));
		}
		else
		{
			// New item.
			if ($catId)
			{
				$item->params->set('access-change', User::authorise('core.edit.state', 'com_content.category.' . $catId));
				$item->catid = $catId;
			}
			else
			{
				$item->params->set('access-change', User::authorise('core.edit.state', 'com_content'));
			}
		}

		$item->articletext = $item->introtext;
		if (!empty($item->fulltext))
		{
			$item->articletext .= '<hr id="system-readmore" />' . $item->fulltext;
		}

		if (empty($item->id))
		{
			$authorised = User::authorise('core.create', 'com_content') || count(User::getAuthorisedCategories('com_content', 'core.create'));
		}
		else
		{
			$authorised = $item->params->get('access-edit');
		}

		if ($authorised !== true)
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		if (!empty($item) && isset($item->id))
		{
			$item->images = json_decode($item->images);
			$item->urls   = json_decode($item->urls);

			$tmp = new stdClass;
			$tmp->images = $item->images;
			$tmp->urls   = $item->urls;

			$form->bind($tmp);
		}

		$form = $item->getForm('site');

		if ($params->get('enable_category') == 1)
		{
			$form->setFieldAttribute('catid', 'default', $params->get('catid', 1));
			$form->setFieldAttribute('catid', 'readonly', 'true');
		}

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = App::get('menu')->getActive();
		if ($menu)
		{
			$params->def('page_heading', $params->get('page_title', $menu->title));
		}
		else
		{
			$params->def('page_heading', Lang::txt('COM_CONTENT_FORM_EDIT_ARTICLE'));
		}

		$title = $params->def('page_title', Lang::txt('COM_CONTENT_FORM_EDIT_ARTICLE'));
		if (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}
		Document::setTitle($title);

		Pathway::append($title, '');

		if ($params->get('menu-meta_description'))
		{
			Document::setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			Document::setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			Document::setMetadata('robots', $params->get('robots'));
		}

		// Escape strings for HTML output
		$pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
		$return_page = base64_encode(urlencode($return_page));

		$this->view
			->setName('form')
			->setLayout(Request::getCmd('layout', 'edit'))
			->set('pageclass_sfx', $pageclass_sfx)
			->set('params', $params)
			->set('item', $item)
			->set('form', $form)
			->set('return_page', $return_page)
			->set('user', User::getInstance())
			->display();
	}

	/**
	 * Cancel editing
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		$id = Request::getInt('id');
		$data = Request::getArray('fields', array(), 'post');
		$context = "com_content.article.$id";

		$model = Article::oneOrNew($id)->set($data);

		// Check general edit permission first.
		$authorised = false;

		if (User::authorise('core.edit', $context))
		{
			$authorised = true;
		}
		// Now check if edit.own is available.
		elseif (User::get('id') && User::authorise('core.edit.own', $asset))
		{
			// Check for a valid user and that they are the owner.
			if (User::get('id') == $model->created_by)
			{
				$authorised = true;
			}
		}

		// Check edit state permission.
		if ($id)
		{
			// Existing item
			$authorised = User::authorise('core.edit.state', $asset);
		}
		else
		{
			// New item.
			if ($catId)
			{
				$authorised = User::authorise('core.edit.state', 'com_content.category.' . $catId);
			}
			else
			{
				$authorised = User::authorise('core.edit.state', 'com_content');
			}
		}

		if (empty($model->id))
		{
			$authorised = User::authorise('core.create', 'com_content') || count(User::getAuthorisedCategories('com_content', 'core.create'));
		}

		if ($authorised !== true)
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		if ($model->isCheckedOut() && $model->get('checked_out') != User::get('id'))
		{
			// Redirect back to the edit screen.
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));

			return $this->cancelTask();
		}

		$model->checkout();

		/* @TODO: Method doesn't return anything. Probably should return a boolean.
		if (!$model->checkout())
		{
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()))
		}*/

		// Attempt to save the data.
		if (!$model->save())
		{
			// Save the data in the session.
			User::setState($context . '.data', $data);

			// Redirect back to the edit screen.
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
		}

		$model->checkin();

		/* @TODO: Method doesn't return anything. Probably should return a boolean.
		if (!$model->checkin())
		{
			Notify::error(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()))
		}*/

		User::setState($context . '.data', null);

		$this->cancelTask();
	}

	/**
	 * Cancel editing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		$id = Request::getInt('id');

		$model = Article::oneOrNew($id);

		if ($model->isCheckedOut() && $model->get('checked_out') == User::get('id'))
		{
			$model->checkin();
		}

		$return_page = urldecode(base64_decode(Request::getString('return', null)));

		if (empty($return_page) || !\Hubzero\Utility\Uri::isInternal($return_page))
		{
			$return_page = Request::base();
		}

		App::redirect($return_page);
	}
}
