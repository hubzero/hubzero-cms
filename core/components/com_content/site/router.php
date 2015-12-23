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
namespace Components\Content\Site;

use Hubzero\Component\Router\Base;
use Component;
use App;

jimport('joomla.application.categories');

/**
 * Routing class for the component
 */
class Router extends Base
{
	/**
	 * Post buld routine.
	 *
	 * @param   array  $segments  The URL arguments to use to assemble the subsequent URL.
	 * @return  array
	 */
	protected function postBuild($segments)
	{
		$total = count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	/**
	 * Build the route for the component.
	 *
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$segments = array();

		// get a menu item based on Itemid or currently active
		$menu     = App::get('menu');
		$params   = Component::params('com_content');
		$advanced = $params->get('sef_advanced_link', 0);

		// we need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}

		if (isset($query['view']))
		{
			$view = $query['view'];
		}
		else
		{
			// we need to have a view in the query or it is an invalid URL
			return $this->postBuild($segments);
		}

		// are we dealing with an article or category that is attached to a menu item?
		if (($menuItem instanceof \stdClass)
		 && $menuItem->query['view'] == $query['view']
		 && isset($query['id'])
		 && $menuItem->query['id'] == intval($query['id']))
		{
			unset($query['view']);

			if (isset($query['catid']))
			{
				unset($query['catid']);
			}

			if (isset($query['layout']))
			{
				unset($query['layout']);
			}

			unset($query['id']);

			return $segments;
		}

		if ($view == 'category' || $view == 'article')
		{
			if (!$menuItemGiven)
			{
				$segments[] = $view;
			}

			unset($query['view']);

			if ($view == 'article')
			{
				if (isset($query['id']) && isset($query['catid']) && $query['catid'])
				{
					$catid = $query['catid'];
					// Make sure we have the id and the alias
					if (strpos($query['id'], ':') === false)
					{
						$db = App::get('db');
						$aquery = $db->setQuery($db->getQuery(true)
							->select('alias')
							->from('#__content')
							->where('id=' . (int)$query['id'])
						);
						$alias = $db->loadResult();
						$query['id'] = $query['id'] . ':' . $alias;
					}
				}
				else
				{
					// we should have these two set for this view.  If we don't, it is an error
					return $this->postBuild($segments);
				}
			}
			else
			{
				if (isset($query['id']))
				{
					$catid = $query['id'];
				}
				else
				{
					// we should have id set for this view.  If we don't, it is an error
					return $this->postBuild($segments);
				}
			}

			if ($menuItemGiven && isset($menuItem->query['id']))
			{
				$mCatid = $menuItem->query['id'];
			}
			else
			{
				$mCatid = 0;
			}

			$categories = \JCategories::getInstance('Content');
			$category   = $categories->get($catid);

			if (!$category)
			{
				// we couldn't find the category we were given.  Bail.
				return $this->postBuild($segments);
			}

			$path = array_reverse($category->getPath());

			$array = array();

			foreach ($path as $id)
			{
				if ((int)$id == (int)$mCatid)
				{
					break;
				}

				list($tmp, $id) = explode(':', $id, 2);

				$array[] = $id;
			}

			$array = array_reverse($array);

			if (!$advanced && count($array))
			{
				$array[0] = (int)$catid . ':' . $array[0];
			}

			$segments = array_merge($segments, $array);

			if ($view == 'article')
			{
				if ($advanced)
				{
					list($tmp, $id) = explode(':', $query['id'], 2);
				}
				else
				{
					$id = $query['id'];
				}
				$segments[] = $id;
			}
			unset($query['id']);
			unset($query['catid']);
		}

		if ($view == 'archive')
		{
			if (!$menuItemGiven)
			{
				$segments[] = $view;
				unset($query['view']);
			}

			if (isset($query['year']))
			{
				if ($menuItemGiven)
				{
					$segments[] = $query['year'];
					unset($query['year']);
				}
			}

			if (isset($query['year']) && isset($query['month']))
			{
				if ($menuItemGiven)
				{
					$segments[] = $query['month'];
					unset($query['month']);
				}
			}
		}

		// if the layout is specified and it is the same as the layout in the menu item, we
		// unset it so it doesn't go into the query string.
		if (isset($query['layout']))
		{
			if ($menuItemGiven && isset($menuItem->query['layout']))
			{
				if ($query['layout'] == $menuItem->query['layout'])
				{
					unset($query['layout']);
				}
			}
			else
			{
				if ($query['layout'] == 'default')
				{
					unset($query['layout']);
				}
			}
		}

		return $this->postBuild($segments);
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$vars = array();

		//Get the active menu item.
		$menu   = App::get('menu');
		$item   = $menu->getActive();
		$params = Component::params('com_content');
		$advanced = $params->get('sef_advanced_link', 0);
		$db     = App::get('db');

		// Count route segments
		$count = count($segments);

		for ($i = 0; $i < $count; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		// Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
		// the first segment is the view and the last segment is the id of the article or category.
		if (!isset($item))
		{
			$vars['view'] = $segments[0];
			$vars['id']   = $segments[$count - 1];

			return $vars;
		}

		// if there is only one segment, then it points to either an article or a category
		// we test it first to see if it is a category.  If the id and alias match a category
		// then we assume it is a category.  If they don't we assume it is an article
		if ($count == 1)
		{
			// we check to see if an alias is given.  If not, we assume it is an article
			if (strpos($segments[0], ':') === false)
			{
				$vars['view'] = 'article';
				$vars['id']   = (int)$segments[0];
				return $vars;
			}

			list($id, $alias) = explode(':', $segments[0], 2);

			// first we check if it is a category
			$category = \JCategories::getInstance('Content')->get($id);

			if ($category && $category->alias == $alias)
			{
				$vars['view'] = 'category';
				$vars['id']   = $id;

				return $vars;
			}
			else
			{
				$query = 'SELECT alias, catid FROM `#__content` WHERE id = ' . (int)$id;
				$db->setQuery($query);
				$article = $db->loadObject();

				if ($article)
				{
					if ($article->alias == $alias)
					{
						$vars['view']  = 'article';
						$vars['catid'] = (int)$article->catid;
						$vars['id']    = (int)$id;

						return $vars;
					}
				}
			}
		}

		// if there was more than one segment, then we can determine where the URL points to
		// because the first segment will have the target category id prepended to it.  If the
		// last segment has a number prepended, it is an article, otherwise, it is a category.
		if (!$advanced)
		{
			$cat_id = (int)$segments[0];

			$article_id = (int)$segments[$count - 1];

			if ($article_id > 0)
			{
				$vars['view']  = 'article';
				$vars['catid'] = $cat_id;
				$vars['id']    = $article_id;
			}
			else
			{
				$vars['view'] = 'category';
				$vars['id']   = $cat_id;
			}

			return $vars;
		}

		// we get the category id from the menu item and search from there
		$id = $item->query['id'];
		$category = \JCategories::getInstance('Content')->get($id);

		if (!$category)
		{
			App::abort(404, \Lang::txt('COM_CONTENT_ERROR_PARENT_CATEGORY_NOT_FOUND'));
			return $vars;
		}

		$categories = $category->getChildren();
		$vars['catid'] = $id;
		$vars['id'] = $id;
		$found = 0;

		foreach ($segments as $segment)
		{
			$segment = str_replace(':', '-', $segment);

			foreach ($categories as $category)
			{
				if ($category->alias == $segment)
				{
					$vars['id']    = $category->id;
					$vars['catid'] = $category->id;
					$vars['view']  = 'category';
					$categories = $category->getChildren();
					$found = 1;
					break;
				}
			}

			if ($found == 0)
			{
				if ($advanced)
				{
					$query = 'SELECT id FROM `#__content` WHERE catid = ' . $vars['catid'] . ' AND alias = ' . $db->Quote($segment);
					$db->setQuery($query);
					$cid = $db->loadResult();
				}
				else
				{
					$cid = $segment;
				}

				$vars['id'] = $cid;

				if ($item->query['view'] == 'archive' && $count != 1)
				{
					$vars['year']  = $count >= 2 ? $segments[$count-2] : null;
					$vars['month'] = $segments[$count-1];
					$vars['view']  = 'archive';
				}
				else
				{
					$vars['view'] = 'article';
				}
			}

			$found = 0;
		}

		return $vars;
	}
}