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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site;

use Hubzero\Component\Router\Base;

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

		if (!empty($query['controller']))
		{
			$segments[] = $query['controller'];
			unset($query['controller']);
		}
		if (!empty($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}
		if (!empty($query['alias']))
		{
			$segments[] = $query['alias'];
			unset($query['alias']);
		}
		if (!empty($query['active']))
		{
			$segments[] = $query['active'];
			unset($query['active']);
		}
		if (!empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		if (!empty($query['category']))
		{
			$segments[] = $query['category'];
			unset($query['category']);
		}
		if (!empty($query['pid']))
		{
			$segments[] = $query['pid'];
			unset($query['pid']);
		}
		if (!empty($query['v']))
		{
			$segments[] = $query['v'];
			unset($query['v']);
		}
		if (!empty($query['a']))
		{
			$segments[] = $query['a'];
			unset($query['a']);
		}
		if (!empty($query['file']))
		{
			$segments[] = $query['file'];
			unset($query['file']);
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
		$vars = array();

		// Valid tasks not requiring id
		$tasks = array(	'browse', 'start', 'submit', 'edit', 'publication');

		if (empty($segments[0]))
		{
			return $vars;
		}
		if (!empty($segments[0]) && $segments[0] == 'curation')
		{
			$vars['controller'] = 'curation';

			if (!empty($segments[1]) && is_numeric($segments[1]))
			{
				$vars['id']   = $segments[1];

				if (!empty($segments[2]))
				{
					$vars['task'] = $segments[2];
				}
				else
				{
					$vars['task'] = 'view';
				}
			}

			return $vars;
		}

		if (is_numeric($segments[0]))
		{
			$vars['task'] = 'view';
			$vars['id']   = $segments[0];
			if (!empty($segments[1]))
			{
				if (is_numeric($segments[1]) || $segments[1] == 'dev' || $segments[1] == 'default')
				{
					$vars['v'] = $segments[1];
				}
			}
		}
		elseif (isset($segments[1]) && $segments[1] == 'submit')
		{
			// Links within projects publications plugin
			$vars['task']   = 'submit';
			$vars['active'] = $segments[0];
			if (!empty($segments[2]) && is_numeric($segments[2]))
			{
				$vars['pid'] = $segments[2];
			}

			return $vars;
		}
		elseif (in_array($segments[0], $tasks))
		{
			$vars['task'] = $segments[0];
			if (!empty($segments[1]))
			{
				if (is_numeric($segments[1]))
				{
					$vars['pid'] = $segments[1];
				}
			}
		}
		else
		{
			include_once( dirname(__DIR__) . DS . 'tables' . DS . 'category.php');

			$database = \App::get('db');

			$t = new \Components\Publications\Tables\Category( $database );
			$cats = $t->getCategories();

			foreach ($cats as $cat)
			{
				if (trim($segments[0]) == $cat->url_alias)
				{
					$vars['category'] = $segments[0];
					$vars['task'] = 'browse';
				}
			}

			if (!isset($vars['category']))
			{
				$vars['alias'] = $segments[0];
				$vars['task'] = 'view';

				if (isset($segments[1]))
				{
					if (is_numeric($segments[1]) || $segments[1] == 'dev' || $segments[1] == 'default')
					{
						$vars['v'] = $segments[1];
					}
				}
			}
		}

		if (!empty($segments[1]))
		{
			switch ($segments[1])
			{
				case 'edit':
					$vars['task'] = 'edit';
					if (is_numeric($segments[0]))
					{
						$vars['pid'] = $segments[0];
						$vars['id']  = '';
					}
					break;

				case 'download':
				case 'wiki':
				case 'play':
				case 'serve':
				case 'video':
					$vars['task'] = $segments[1];

					if (!empty($segments[2]))
					{
						$vars['v'] = $segments[2];
					}
					if (!empty($segments[3]))
					{
						$vars['a'] = $segments[3];
					}

					break;

				case 'citation': $vars['task'] = 'citation'; break;
				case 'feed.rss': $vars['task'] = 'feed';     break;
				case 'feed':     $vars['task'] = 'feed';     break;
				case 'license':  $vars['task'] = 'license';  break;
				case 'main':     $vars['task'] = 'main';     break;

				default:
					if ($segments[0] == 'browse')
					{
						$vars['category'] = $segments[1];
					}
					else
					{
						$vars['active'] = $segments[1];
						//if ($vars['active'] == 'share' && !empty($segments[2]))
						if (isset($segments[2]))
						{
							if (is_numeric($segments[2]) || $segments[1] == 'dev' || $segments[1] == 'default')
							{
								$vars['v'] = $segments[2];
							}
						}
					}
				break;
			}
		}

		// are we serving up a file
		$uri = Request::getVar('REQUEST_URI', '', 'server');
		if (strstr($uri, 'Image:') || strstr($uri, 'File:'))
		{
			$vars['task'] = 'download';
			$vars['controller'] = 'media';
		}

		return $vars;
	}
}
