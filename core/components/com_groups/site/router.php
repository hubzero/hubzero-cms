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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site;

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

		if (!empty($query['task']) && $query['task'] == 'view')
		{
			unset($query['task']);
		}

		if (!empty($query['cn']))
		{
			$segments[] = $query['cn'];
			unset($query['cn']);
		}

		if (!empty($query['gid']))
		{
			//log regardless
			\Log::debug('Group Route Build Path sending gid instead of cn: ' . $_SERVER['REQUEST_URI']);

			$segments[] = $query['gid'];
			unset($query['gid']);
		}

		if (!empty($query['controller']) && $query['controller'] != 'groups')
		{
			$segments[] = $query['controller'];
			unset($query['controller']);
		}

		if (!empty($query['active']))
		{
			$segments[] = $query['active'];
			if ($query['active'] == '' && !empty($query['task']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
			unset($query['active']);
		}
		else
		{
			if ((empty($query['scope']) || $query['scope'] == '') && !empty($query['task']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
		}
		if (!empty($query['scope']))
		{
			$segments[] = $query['scope'];
			unset($query['scope']);
		}
		if (!empty($query['pagename']))
		{
			$segments[] = $query['pagename'];
			unset($query['pagename']);
		}

		//are we on the group calendar
		if (in_array('calendar', $segments))
		{
			if (!empty($query['year']))
			{
				$segments[] = $query['year'];
				unset($query['year']);
			}
			if (!empty($query['month']))
			{
				$segments[] = $query['month'];
				unset($query['month']);
			}
			if (!empty($query['action']))
			{
				$segments[] = $query['action'];
				unset($query['action']);
			}
			if (!empty($query['event_id']))
			{
				$segments[] = $query['event_id'];
				unset($query['event_id']);
			}
			if (!empty($query['calendar_id']))
			{
				$segments[] = $query['calendar_id'];
				unset($query['calendar_id']);
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

		if (empty($segments))
		{
			return $vars;
		}

		if ($segments[0] == 'new' || $segments[0] == 'browse' || $segments[0] == 'features')
		{
			$vars['task'] = $segments[0];
		}
		else
		{
			$vars['controller'] = 'groups';
			$vars['task'] = 'view';
			$vars['cn'] = $segments[0];
		}

		if (isset($segments[1]))
		{
			switch ($segments[1])
			{
				case 'edit':
				case 'delete':
				case 'customize':
					$vars['task'] = $segments[1];
					break;
				case 'invite':
				case 'accept':
				case 'cancel':
				case 'join':
				case 'request':
					$vars['task'] = $segments[1];
					$vars['controller'] = 'membership';
					break;
				case 'pages':
				case 'modules':
				case 'categories':
				case 'media':
					$vars['controller'] = $segments[1];
					break;
				default:
					$vars['active'] = $segments[1];
					$this->handleGroupComponents($vars);
			}
		}

		if (isset($segments[2]))
		{
			if (isset($vars['controller']) && in_array($vars['controller'], array('pages', 'media', 'categories', 'modules')))
			{
				$vars['task'] = $segments[2];
			}
			else if ($segments[1] == 'wiki')
			{
				if (isset($segments[3]) && preg_match('/File:|Image:/', $segments[3]))
				{
					$vars['pagename'] = $segments[2];
				}
				else
				{
					$vars['pagename'] = array_pop($segments);
				}

				$s = implode(DS, $segments);
				$vars['scope'] = $s;
			}
			else
			{
				$vars['action'] = $segments[2];
			}
		}

		//are we on the calendar
		if (isset($vars['active']) && $vars['active'] == 'calendar')
		{
			if (isset($segments[2]))
			{
				if (is_numeric($segments[2]))
				{
					$vars['year'] = $segments[2];
				}
				else
				{
					$vars['action'] = $segments[2];
				}
			}

			if (isset($segments[3]))
			{
				if (isset($vars['year']))
				{
					$vars['month'] = $segments[3];
				}
				else
				{
					if (in_array($vars['action'], array('events', 'editcalendar', 'deletecalendar', 'refreshcalendar', 'subscribe')))
					{
						$vars['calendar_id'] = $segments[3];
					}
					else
					{
						$vars['event_id'] = $segments[3];
					}
				}
			}
		}

		// if we have a cname isnt all lowercase
		if (isset($vars['cn']) && $vars['cn'] != strtolower($vars['cn']))
		{
			// make sure we have a group with the lowercase version
			$cname = strtolower($vars['cn']);
			$group = \Hubzero\User\Group::getInstance($cname);

			if (is_object($group))
			{
				// replace cn with lowercase version
				$vars['cn'] = $cname;

				// add option var
				$vars['option'] = 'com_groups';

				// build url to redirect to based on vars
				\App::redirect(\Route::url('index.php?' . http_build_query($vars)), null, null, true);
				exit();
			}
		}

		return $vars;
	}

	/**
	 * Special function that takes all extra query params and prefixes them
	 *
	 * This is needeed when users use controller & task query string params which
	 * conflict with the groups component controller & task query string params. Prefixing
	 * them and setting the original key to what the GroupsParseRoute method generates. Then
	 * the supergroup system plugin rewrites them back after we made it through to the group component.
	 *
	 * @param   array  $vars  Query values
	 * @return  void
	 */
	public function handleGroupComponents($vars)
	{
		// make sure we have an active vars
		if (isset($vars['active']))
		{
			// load our group
			$group = \Hubzero\User\Group::getInstance($vars['cn']);
			if (!$group || !$group->isSuperGroup())
			{
				return;
			}

			// build upload path
			$groupsConfig = \Component::params('com_groups');
			$uploadPath = trim($groupsConfig->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');

			// build path to component
			$componentPath = PATH_APP . DS . $uploadPath . DS . 'components' . DS . 'com_' . $vars['active'];

			// make sure its a component
			if (!is_dir($componentPath))
			{
				return;
			}

			// rewrite all query string params to have "g_" prefix
			foreach (\Request::query() as $k => $v)
			{
				$old = (isset($vars[$k])) ? $vars[$k] : null;
				\Request::setVar('sg_' . $k, $v);
				\Request::setVar($k, $old);
			}
		}
	}
}
