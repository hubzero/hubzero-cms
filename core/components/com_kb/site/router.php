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

				include_once(dirname(__DIR__) . DS . 'models' . DS . 'archive.php');

				$category = Category::all()
					->whereEquals('alias', $categoryAlias)
					->row();
				$categoryId = $category->get('id');

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
					$vars['task'] = 'article';
					if (isset($vars['alias']) && $vars['alias'])
					{
						$vars['category'] = $vars['alias'];
					}
					else
					{
						$vars['category'] = $segments[0];
					}
					$vars['alias'] = urldecode($segments[2]);
					$vars['alias'] = str_replace(':', '-', $vars['alias']);
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
