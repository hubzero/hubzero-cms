<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Kb\Site;

use Hubzero\Component\Router\Base;
use Components\Kb\Tables\Category;
use Components\Kb\Tables\Article;

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
				$vars['alias'] = urldecode($segments[0]);
				$vars['alias'] = str_replace(':', '-', $vars['alias']);
			break;

			case 2:
				$title1 = urldecode($segments[0]);
				$title1 = str_replace(':', '-', $title1);
				$title2 = urldecode($segments[1]);
				$title2 = str_replace(':', '-', $title2);

				include_once(dirname(__DIR__) . DS . 'models' . DS . 'archive.php');

				$db = \App::get('db');

				$category = new Category($db);
				$category->loadAlias($title2);

				if ($category->id)
				{
					// section/category
					$vars['task'] = 'category';
					$vars['alias'] = $title2; //urldecode($segments[1]);
					return $vars;
				}
				else
				{
					$category->loadAlias($title1);
				}

				if (!$category->id)
				{
					$vars['alias'] = $title2;
					$vars['task'] = 'article';
					$vars['category'] = $title1;
					return $vars;
				}

				$article = new Article($db);
				$article->loadAlias($title2, $category->id);

				if ($article->id)
				{
					// section/article
					$vars['id'] = $article->id;
					$vars['task'] = 'article';
					//$vars['alias'] = $title2; //urldecode($segments[1]);
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
