<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Site;

use Hubzero\Component\Router\Base;
use Components\Kb\Models\Category;
use Components\Kb\Models\Article;

/**
 * Routing class for the component
 */
class Router extends Base
{
	/**
	 * Build the route for the component.
	 *
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$segments = array();

		if (isset($query['task']))
		{
			if ($query['task'] == 'article')
			{
				unset($query['task']);
			}
			else if ($query['task'] == 'vote')
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
		}

		if (isset($query['section']))
		{
			if (!empty($query['section']))
			{
				$segments[] = $query['section'];
			}
			unset($query['section']);
		}

		if (isset($query['category']))
		{
			if (!empty($query['category']))
			{
				$segments[] = $query['category'];
			}
			unset($query['category']);
		}

		if (isset($query['alias']))
		{
			if (!empty($query['alias']))
			{
				$segments[] = $query['alias'];
			}
			unset($query['alias']);
		}

		if (isset($query['id']))
		{
			if (!empty($query['id']))
			{
				$segments[] = $query['id'];
			}
			unset($query['id']);
		}

		if (isset($query['vote']))
		{
			if (!empty($query['vote']))
			{
				$segments[] = $query['vote'];
			}
			unset($query['vote']);
		}

		if (isset($query['controller']))
		{
			unset($query['controller']);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$vars  = array();

		$vars['task'] = 'categories';

		if (empty($segments[0]))
		{
			return $vars;
		}

		$count = count($segments);

		// section/
		switch ($count)
		{
			case 1:
				$vars['task'] = 'category';
				$vars['categoryAlias'] = urldecode($segments[0]);
				$vars['categoryAlias'] = str_replace(':', '-', $vars['categoryAlias']);
			break;

			case 2:
				$categoryAlias = urldecode($segments[0]);
				$categoryAlias = str_replace(':', '-', $categoryAlias);
				$articleAlias = urldecode($segments[1]);
				$articleAlias = str_replace(':', '-', $articleAlias);

				include_once dirname(__DIR__) . DS . 'models' . DS . 'archive.php';

				$category = Category::all()
					->whereEquals('alias', $categoryAlias)
					->whereEquals('published', Category::STATE_PUBLISHED)
					->row();
				$categoryId = $category->get('id');

				// Check if the next segment is a category
				// If not, then we assume it's an article
				$category = Category::all()
					->whereEquals('alias', $articleAlias)
					->whereEquals('published', Category::STATE_PUBLISHED)
					->row();
				if ($category && $category->get('id'))
				{
					$vars['task'] = 'category';
					$vars['categoryAlias'] = $articleAlias;
					return $vars;
				}

				if ($categoryId && $articleAlias)
				{
					$vars['articleAlias'] = $articleAlias;
					$vars['task'] = 'article';
					$vars['categoryId'] = $categoryId;
					return $vars;
				}
			break;

			case 3:
				// section/category/article
				// section/article/comments.rss
				if ($segments[2] == 'comments.rss')
				{
					$vars['task'] = 'comments';
					$vars['alias'] = urldecode($segments[1]);
					$vars['alias'] = str_replace(':', '-', $vars['alias']);
					$vars['category'] = $segments[0];
				}
				else
				{
					include_once dirname(__DIR__) . DS . 'models' . DS . 'archive.php';

					$categoryAlias = urldecode($segments[1]);
					$categoryAlias = str_replace(':', '-', $categoryAlias);

					$category = Category::all()
						->whereEquals('alias', $categoryAlias)
						->whereEquals('published', Category::STATE_PUBLISHED)
						->row();
					$vars['categoryId'] = $category->get('id');

					$vars['task'] = 'article';
					$vars['articleAlias'] = urldecode($segments[2]);
					$vars['articleAlias'] = str_replace(':', '-', $vars['articleAlias']);
				}
			break;

			case 4:
				// task/category/id/vote
				// section/category/article/comments.rss
				if ($segments[3] == 'comments.rss')
				{
					$vars['task']  = 'comments';
					$vars['alias'] = urldecode($segments[2]);
					$vars['alias'] = str_replace(':', '-', $vars['alias']);
					$vars['category'] = $segments[1];
				}
				else
				{
					$vars['task'] = $segments[0];
					$vars['type'] = $segments[1];
					$vars['id']   = $segments[2];
					$vars['vote'] = $segments[3];
				}
			break;
		}

		return $vars;
	}
}
