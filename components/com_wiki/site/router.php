<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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

		if (!empty($query['scope']))
		{
			$segments[] = $query['scope'];
		}
		unset($query['scope']);
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

		//$vars['task'] = 'view';
		$e = array_pop($segments);
		$s = implode(DS, $segments);
		if ($s)
		{
			$vars['scope'] = $s;
		}
		$vars['pagename'] = $e;

		if (!isset($vars['task']) || !$vars['task'])
		{
			$vars['task'] = \JRequest::getWord('task', '');
		}

		switch ($vars['task'])
		{
			case 'upload':
			case 'download':
			case 'deletefolder':
			case 'deletefile':
			case 'media':
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

		return $vars;
	}
}