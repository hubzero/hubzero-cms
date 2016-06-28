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

namespace Components\Projects\Api;

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

		$vars['controller'] = 'projects';

		if (isset($segments[0]))
		{
			//  /projects
			//  /projects/list
			//  /projects/##
			//  /projects/##/files
			//  /projects/##/files/list
			//  /projects/##/files/insert
			//  /projects/##/files/update
			//  /projects/##/files/get
			//  /projects/##/files/delete
			//  /projects/##/files/makedirectory
			//  /projects/##/files/rename
			//  /projects/##/files/move
			//  /projects/##/team
			//  /projects/##/team/list
			//  /projects/##/publications
			//  /projects/##/publications/list
			//  /projects/##/files/connections
			//  /projects/##/files/connections/##/list
			//  /projects/##/files/connections/##/insert
			//  /projects/##/files/connections/##/update
			//  /projects/##/files/connections/##/get
			//  /projects/##/files/connections/##/delete
			//  /projects/##/files/connections/##/makedirectory
			//  /projects/##/files/connections/##/rename
			//  /projects/##/files/connections/##/move
			//  /projects/##/files/connections/##/upload
			//  /projects/##/files/connections/##/download
			//  /projects/##/files/connections/##/getmetadata
			//  /projects/##/files/connections/##/setmetadata

			if (isset($segments[1]) && $segments[1] == 'files')
			{
				$vars['controller'] = 'files';
				$vars['id']         = $segments[0];
				if ((isset($segments[3])) && ($segments[2] == 'connections'))
				{
					$vars['cid'] = $segments[3];
					$vars['task'] = isset($segments[4]) ? $segments[4] : 'list';
				}
				else
				{
					$vars['task'] = isset($segments[2]) ? $segments[2] : 'list';
				}
			}
			elseif (isset($segments[1]) && $segments[1] == 'team')
			{
				$vars['controller'] = 'team';
				$vars['id']         = $segments[0];
				$vars['task']       = isset($segments[2]) ? $segments[2] : 'list';
			}
			elseif (isset($segments[1]) && $segments[1] == 'publications')
			{
				$vars['controller'] = 'publications';
				$vars['id']         = $segments[0];
				$vars['task']       = isset($segments[2]) ? $segments[2] : 'list';
			}
			elseif ($segments[0] != 'list')
			{
				$vars['id']   = $segments[0];
				$vars['task'] = 'get';
			}
			else
			{
				$vars['task'] = $segments[0];
			}
		}

		return $vars;
	}
}
