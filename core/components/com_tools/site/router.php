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

namespace Components\Tools\Site;

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
			if ($query['controller'] == 'media')
			{
				$segments[] = $query['controller'];
			}
			unset($query['controller']);
		}
		if (!empty($query['app']))
		{
			$segments[] = $query['app'];
			unset($query['app']);
		}
		if (!empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		if (!empty($query['version']))
		{
			$segments[] = $query['version'];
			unset($query['version']);
		}
		/*if (isset($query['sess']))
		{
			$segments[] = $query['sess'];
			unset($query['sess']);
		}*/
		if (isset($query['return']) && $query['return'] == '')
		{
			unset($query['return']);
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
				case 'zones':
					$vars['controller'] = $segments[0];
				break;

				case 'media':
					$vars['controller'] = $segments[0];
				break;

				case 'pipeline':
				case 'create':
					$vars['task'] = $segments[0];
					$vars['controller'] = 'pipeline';
				break;

				case 'login':
				case 'accessdenied':
				case 'quotaexceeded':
				case 'rename':
					$vars['task'] = $segments[0];
					$vars['controller'] = 'sessions';
				break;

				case 'assets':
					if (count($segments) < 3)
					{
						break;
					}
					$vars['task'] = 'assets';
					$vars['controller'] = 'tools';
					$vars['type'] = $segments[1];
					$vars['file'] = $segments[2];
					return $vars;
				break;

				case 'images':
					$vars['task'] = $segments[0];
					$vars['controller'] = 'tools';
				break;

				case 'diskusage':
				case 'storageexceeded':
				case 'storage':
				case 'filelist':
				case 'deletefolder':
				case 'deletefile':
				case 'purge':
					$vars['task'] = $segments[0];
					$vars['controller'] = 'storage';
				break;

				case 'reorder':
				case 'remove':
				case 'save':
					$vars['option'] = 'com_tools';
					$vars['controller'] = \Request::getVar('controller', 'authors');
					$vars['task'] = $segments[0];
				break;

				default:
					// This is an alias
					// /tools/mytool => /resources/mytool
					$vars['option'] = 'com_resources';
					$vars['alias'] = $segments[0];
				break;
			}
		}

		if (isset($segments[1]))
		{
			switch ($segments[1])
			{
				case 'delete':
					if (isset($vars['controller']) && $vars['controller'] == 'media')
					{
						$vars['task'] = $segments[1];
					}
				break;

				case 'publish':
				case 'install':
				case 'retire':
				case 'addrepo':
					$vars['option'] = 'com_tools';
					$vars['controller'] = 'admin';
					$vars['app'] = $segments[0];
					$vars['task'] = $segments[1];
				break;

				// Pipeline controller
				case 'register':
				case 'edit':
				case 'save':
				case 'update':
				case 'message':
				case 'cancel':
				case 'create':
				case 'versions':
				case 'saveversion':
				case 'finalizeversion':
				case 'license':
				case 'savelicense':
				case 'finalize':
				case 'releasenotes':
				case 'savenotes':
				case 'start':
				case 'wiki':
				case 'status':
					$vars['option'] = 'com_tools';
					$vars['controller'] = 'pipeline';
					$vars['app'] = $segments[0];
					$vars['task'] = $segments[1];
				break;

				// Resource controller
				case 'preview':
				case 'resource':
				case 'resources':
					$vars['option'] = 'com_tools';
					$vars['controller'] = 'resources';
					$vars['app'] = $segments[0];
					if ($segments[1] == 'preview')
					{
						$vars['task'] = $segments[1];
					}
				break;

				// Sessions controller
				case 'reinvoke':
				case 'invoke':
					$vars['option'] = 'com_tools';
					$vars['controller'] = 'sessions';
					$vars['app'] = $segments[0];
					$vars['task'] = $segments[1];
					if (isset($segments[2])) {
						$vars['version'] = $segments[2];
					}
				break;

				case 'session':
				case 'share':
				case 'unshare':
				case 'stop':
					$vars['option'] = 'com_tools';
					$vars['controller'] = 'sessions';
					$vars['app'] = $segments[0];
					if ($segments[1] == 'session')
					{
						$vars['task'] = 'view';
					}
					else
					{
						$vars['task'] = $segments[1];
					}
					if (isset($segments[2]))
					{
						$vars['sess'] = $segments[2];
					}
				break;

				// Tools controller
				case 'report':
					\App::redirect(\Route::url('index.php?option=com_support&task=tickets&find=group:app-' . $segments[0]));
					exit();
				break;

				case 'forge.png':
					$vars['task'] = 'image';
					$vars['controller'] = 'tools';
				break;

				case 'site_css.cs':
				case 'site_css.css':
					$vars['task'] = 'css';
					$vars['controller'] = 'tools';
				break;

				default:
					if (isset($vars['controller']) && $vars['controller'] == 'zones')
					{
						$vars['task'] = $segments[1];
						if (isset($segments[2]))
						{
							$vars['id'] = $segments[2];
						}
					}
					else
					{
						$vars['sess'] = $segments[1];
					}
				break;
			}
		}

		return $vars;
	}
}
