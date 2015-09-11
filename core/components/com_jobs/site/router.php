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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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