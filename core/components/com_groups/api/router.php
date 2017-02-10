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

namespace Components\Groups\Api;

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

		if (!empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
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

		$vars['controller'] = 'groups';

		if (isset($segments[0]))
		{
			// /groups/{id|cn}
			if (is_numeric($segments[0]) || !in_array($segments[0], array('list', 'create')))
			{
				$vars['id'] = $segments[0];
				if (\App::get('request')->method() == 'GET')
				{
					$vars['task'] = 'read';
				}
			}
			// /groups/list
			// /groups/create
			else
			{
				$vars['task'] = $segments[0];
			}

			if (isset($segments[1]))
			{
				// /groups/{id|cn}/read
				// /groups/{id|cn}/update
				// /groups/{id|cn}/delete
				if (in_array($segments[1], array('read', 'update', 'delete')))
				{
					$vars['task'] = $segments[1];
				}
				// /groups/{id|cn}/{plugin}
				else
				{
					$vars['controller'] = 'plugins';
					$vars['task']       = 'index';
					$vars['active']     = $segments[1];

					if ($segments[1] == 'members')
					{
						$vars['controller'] = $segments[1];
					}

					// /groups/{id|cn}/{plugin}/list
					// /groups/{id|cn}/{plugin}/create
					// /groups/{id|cn}/{plugin}/{record}
					if (isset($segments[2]))
					{
						// /groups/{id|cn}/{plugin}/{record}
						if (is_numeric($segments[2]))
						{
							$vars['record_id'] = $segments[2];
							if (\App::get('request')->method() == 'GET')
							{
								$vars['task'] = 'read';
							}
							if (isset($segments[3]))
							{
								if (in_array($segments[3], array('read', 'update', 'delete')))
								{
									$vars['task'] = $segments[3];
								}
							}
						}
						// /groups/{id|cn}/{plugin}/list
						// /groups/{id|cn}/{plugin}/create
						else
						{
							$vars['task'] = $segments[2];
						}
					}
				}
			}
		}

		return $vars;
	}
}
