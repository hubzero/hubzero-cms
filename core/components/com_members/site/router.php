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

namespace Components\Members\Site;

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

		if (!empty($query['id']))
		{
			if (substr($query['id'], 0, 1) == '-')
			{
				$query['id'] = 'n' . substr($query['id'], 1);
			}
			$segments[] = $query['id'];
			unset($query['id']);
		}

		if (!empty($query['active']))
		{
			$segments[] = $query['active'];
			unset($query['active']);

			if (!empty($query['task']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
		}

		if (!empty($query['controller']) && $query['controller'] == 'register')
		{
			$segments[] = $query['controller'];
			unset($query['controller']);
		}

		if (empty($query['id']) && !empty($query['task']))
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

		if (empty($segments))
		{
			return $vars;
		}

		if (isset($segments[0]))
		{
			switch ($segments[0])
			{
				case 'confirm':
					$vars['controller'] = 'register';
					$vars['task'] = $segments[0];
					return $vars;
				break;

				case 'contributors':
					$vars['controller'] = 'profiles';
					$vars['task'] = 'browse';
					$vars['show'] = 'contributors';
				break;

				case 'register':
					$vars['controller'] = $segments[0];
					if (isset($segments[1]))
					{
						$vars['task'] = $segments[1];
					}
					return $vars;
				break;

				case 'login':
				case 'logout':
				case 'remind':
				case 'reminding':
				case 'reset':
				case 'resetting':
				case 'verify':
				case 'verifying':
				case 'setpassword':
				case 'settingpassword':
					$vars['controller'] = 'credentials';
					$vars['task'] = $segments[0];
					return $vars;
				break;

				case 'myaccount':
					if (!\User::get('guest'))
					{
						$vars['id'] = User::get('id');
					}
					else
					{
						$vars['task'] = 'myaccount';
					}
					break;
				case 'activity':
				case 'autocomplete':
					$vars['task'] = $segments[0];
				break;
				case 'vips':
					$vars['task'] = 'browse';
					$vars['show'] = 'vips';
				break;
				case 'browse':
					$vars['task'] = 'browse';
				break;
				default:
					if (isset($segments[0]{0}) && $segments[0]{0} == 'n')
					{
						$vars['id'] = '-' . substr($segments[0],1);
					}
					else
					{
						$vars['id'] = $segments[0];
					}
				break;
			}
		}
		if (isset($segments[1]))
		{
			$userTasks = array(
				'edit',
				'changepassword',
				'raiselimit',
				'cancel',
				'deleteimg',
				'upload',
				'ajaxupload',
				'doajaxupload',
				'ajaxuploadsave',
				'getfileatts',
				'promo-opt-out'
			);
			if (in_array($segments[1], $userTasks))
			{
				$vars['task'] = $segments[1];
				$mediaTasks = array(
					'deleteimg',
					'upload',
					'ajaxupload',
					'doajaxupload',
					'ajaxuploadsave',
					'getfileatts'
				);
				if (in_array($segments[1], $mediaTasks))
				{
					$vars['controller'] = 'media';
				}
			}
			else
			{
				$vars['active'] = $segments[1];

				if (isset($segments[2]))
				{
					if (trim($segments[1]) == 'profile')
					{
						$vars['task'] = $segments[2];
					}
					else
					{
						$vars['action'] = $segments[2];
					}
				}
			}
		}

		// are we serving up a file
		$uri = \Request::getVar('REQUEST_URI', '', 'server');
		if (strstr($uri, 'Image:') || strstr($uri, 'File:'))
		{
			$vars['task'] = 'download';
			$vars['controller'] = 'media';
		}

		return $vars;
	}
}
