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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Site;

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

		if (!empty($query['task']))
		{
			if ($query['task'] == 'new')
			{
				$segments[] = 'question';
				$segments[] = 'new';
			}
			else
			{
				$segments[] = $query['task'];
			}
			unset($query['task']);
		}
		if (!empty($query['tag']))
		{
			$segments[] = $query['tag'];
			unset($query['tag']);
		}
		if (!empty($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}
		if (!empty($query['rid']))
		{
			$segments[] = $query['rid'];
			unset($query['rid']);
		}
		if (!empty($query['controller']))
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
		$vars = array();

		// Count route segments
		$count = count($segments);

		if (empty($segments[0]))
		{
			return $vars;
		}

		switch ($segments[0])
		{
			case 'latest':
			case 'latest.rss':
				$vars['task'] = $segments[0];
				break;
			case 'question':
				if (empty($segments[1]))
				{
					return $vars;
				}

				$vars['task'] = 'question';

				if ($segments[1] == 'new')
				{
					$vars['task'] = 'new';
					if (isset($segments[2]) && $segments[2])
					{
						$vars['tag'] = $segments[2];
					}
					return $vars;
				}

				$vars['id'] = $segments[1];
			break;

			/*case 'tags':
				$vars['task'] = 'tags';
				$vars['tag'] = $segments[1];
			break;

			case 'myquestions':
				$vars['task'] = 'myquestions';
			break;*/

			case 'search':
				$vars['task'] = 'search';
			break;

			case 'answer':
			case 'delete':
			case 'deleteq':
			case 'vote':
			case 'reply':
			case 'math':
				$vars['task'] = $segments[0];
				if (isset($segments[1]))
				{
					$vars['id']   = $segments[1];
				}
			break;

			case 'rateitem':
				$vars['task'] = 'rateitem';
			break;

			case 'savereply':
				$vars['task'] = 'reply';
			break;

			case 'accept':
				$vars['task'] = 'accept';
				$vars['id']   = $segments[1];
				$vars['rid']  = $segments[2];
			break;
		}

		return $vars;
	}
}
