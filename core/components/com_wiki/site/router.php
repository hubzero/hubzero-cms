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

namespace Components\Wiki\Site;

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

		if (isset($query['scope']))
		{
			unset($query['scope']);
		}

		if (!empty($query['pagename']))
		{
			$segments[] = $query['pagename'];
		}
		unset($query['pagename']);

		unset($query['controller']);

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

		if (empty($segments))
		{
			return $vars;
		}

		$vars['pagename'] = end($segments);

		if (!isset($vars['task']) || !$vars['task'])
		{
			$vars['task'] = \Request::getWord('task', '');
		}

		switch ($vars['task'])
		{
			case 'upload':
			case 'download':
			case 'deletefolder':
			case 'deletefile':
			case 'media':
			case 'list':
				$vars['controller'] = 'media';
			break;

			case 'history':
			case 'compare':
			case 'approve':
			case 'deleterevision':
				$vars['controller'] = 'history';
			break;

			case 'editcomment':
			case 'addcomment':
			case 'savecomment':
			case 'reportcomment':
			case 'removecomment':
			case 'comments':
				$vars['controller'] = 'comments';
			break;

			case 'delete':
			case 'edit':
			case 'save':
			case 'rename':
			case 'saverename':
			case 'approve':
			default:
				$vars['controller'] = 'page';
			break;
		}

		if (substr(strtolower($vars['pagename']), 0, strlen('image:')) == 'image:'
		 || substr(strtolower($vars['pagename']), 0, strlen('file:')) == 'file:')
		{
			$vars['controller'] = 'media';
			$vars['task'] = 'download';
		}

		$vars['pagename'] = implode('/', $segments);

		return $vars;
	}
}
