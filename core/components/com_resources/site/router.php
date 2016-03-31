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

namespace Components\Resources\Site;

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

		if (!empty($query['task']) && in_array($query['task'], array('new', 'draft', 'start', 'retract', 'delete', 'discard', 'remove', 'reorder', 'access')))
		{
			if (!empty($query['task']))
			{
				if ($query['task'] == 'start')
				{
					$query['task'] = 'draft';
				}
				$segments[] = $query['task'];
				unset($query['task']);
			}
			if (!empty($query['id']))
			{
				$segments[] = $query['id'];
				unset($query['id']);
			}
		}
		else
		{
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
			if (!empty($query['file']))
			{
				$segments[] = $query['file'];
				unset($query['file']);
			}
			if (!empty($query['type']))
			{
				$segments[] = $query['type'];
				unset($query['type']);
			}
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

		if (empty($segments[0]))
		{
			return $vars;
		}

		if (is_numeric($segments[0]))
		{
			$vars['id'] = $segments[0];
		}
		elseif (in_array($segments[0], array('browse', 'license', 'sourcecode', 'plugin')))
		{
			$vars['task'] = $segments[0];
		}
		elseif (in_array($segments[0], array('new', 'draft', 'start', 'retract', 'delete', 'discard', 'remove', 'reorder', 'access')))
		{
			$vars['task'] = $segments[0];
			$vars['controller'] = 'create';
			if (isset($segments[1]))
			{
				$vars['id'] = $segments[1];
			}
		}
		else
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');

			$database = \App::get('db');

			$t = new \Components\Resources\Tables\Type($database);
			$types = $t->getMajorTypes();

			// Normalize the title
			// This is so we can determine the type of resource to display from the URL
			// For example, /resources/learningmodules => Learning Modules
			for ($i = 0; $i < count($types); $i++)
			{
				//$normalized = preg_replace("/[^a-zA-Z0-9]/", '', $types[$i]->type);
				//$normalized = strtolower($normalized);

				if (trim($segments[0]) == $types[$i]->alias)
				{
					$vars['type'] = $segments[0];
					$vars['task'] = 'browsetags';
				}
			}

			if ($segments[0] == 'license')
			{
				$vars['task'] = $segments[0];
			}
			else
			{
				if (!isset($vars['type']))
				{
					$vars['alias'] = $segments[0];
				}
			}
		}

		if (!empty($segments[1]))
		{
			switch ($segments[1])
			{
				case 'download':
					$vars['task'] = 'download';
					if (isset($segments[2]))
					{
						$vars['file'] = $segments[2];
					}
				break;
				case 'play':     $vars['task'] = 'play';     break;
				case 'watch':    $vars['task'] = 'watch';    break;
				case 'video':    $vars['task'] = 'video';    break;
				//case 'license':  $vars['task'] = 'license';  break;
				case 'citation': $vars['task'] = 'citation'; break;
				case 'feed.rss': $vars['task'] = 'feed';     break;
				case 'feed':     $vars['task'] = 'feed';     break;

				case 'license':
				case 'sourcecode':
					$vars['tool'] = $segments[1];
				break;

				default:
					if ($segments[0] == 'browse')
					{
						$vars['type'] = $segments[1];
					}
					else
					{
						$vars['active'] = $segments[1];
					}
				break;
			}
		}

		return $vars;
	}
}
