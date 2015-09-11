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

namespace Components\Jobs\Site;

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
			if ($query['task'] != 'all')
			{
				$segments[] = $query['task'];
			}
			unset($query['task']);
		}

		if (!empty($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}

		if (!empty($query['code']))
		{
			$segments[] = $query['code'];
			unset($query['code']);
		}

		if (!empty($query['employer']))
		{
			$segments[] = $query['employer'];
			unset($query['employer']);
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
			// default to all jobs
			$vars['task'] = 'all';
			return $vars;
		}

		if (!intval($segments[0]) && empty($segments[1]))
		{
			// some general task
			$vars['task'] = $segments[0];
			return $vars;
		}

		if (!empty($segments[1]))
		{
			switch ($segments[0])
			{
				case 'job':
					$vars['task'] = 'job';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'editjob':
					$vars['task'] = 'editjob';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'editresume':
					$vars['task'] = 'editresume';
					$vars['id'] = $segments[1];
					return $vars;
				break;
				case 'apply':
					$vars['task'] = 'apply';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'editapp':
					$vars['task'] = 'editapp';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'withdraw':
					$vars['task'] = 'withdraw';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'confirmjob':
					$vars['task'] = 'confirmjob';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'unpublish':
					$vars['task'] = 'unpublish';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'reopen':
					$vars['task'] = 'reopen';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'remove':
					$vars['task'] = 'remove';
					$vars['code'] = $segments[1];
					return $vars;
				break;
				case 'browse':
				case 'all':
					$vars['task'] = 'browse';
					$vars['employer'] = $segments[1];
					return $vars;
				break;
			}
		}

		return $vars;
	}
}