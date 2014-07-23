<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Turn querystring parameters into an SEF route
 *
 * @param  array &$query Querystring
 */
function GroupsBuildRoute(&$query)
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
		JFactory::getLogger()->debug("Group JRoute Build Path sending gid instead of cn: " . $_SERVER['REQUEST_URI'] );

		$segments[] = $query['gid'];
		unset($query['gid']);
	}

	if (!empty($query['controller']))
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
 * Parse a SEF route
 *
 * @param  array $segments Exploded route
 * @return array
 */
function GroupsParseRoute($segments)
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
				handleGroupComponents($vars);
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

			$s = implode(DS,$segments);
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
				if (in_array($vars['action'], array('editcalendar','deletecalendar','refreshcalendar', 'subscribe')))
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
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?' . http_build_query($vars)), null, null, true);
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
 * @param  [type] $vars [description]
 * @return [type]       [description]
 */
function handleGroupComponents($vars)
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
		$groupsConfig = JComponentHelper::getParams('com_groups');
		$uploadPath = trim($groupsConfig->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');

		// build path to component
		$componentPath = JPATH_ROOT . DS . $uploadPath . DS . 'components' . DS . 'com_' . $vars['active'];

		// make sure its a component
		if (!is_dir($componentPath))
		{
			return;
		}

		// rewrite all query string params to have "g_" prefix
		foreach (JRequest::get() as $k => $v)
		{
			$old = (isset($vars[$k])) ? $vars[$k] : null;
			JRequest::setVar('sg_' . $k, $v);
			JRequest::setVar($k, $old);
		}
	}
}
