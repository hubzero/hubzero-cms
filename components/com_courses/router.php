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
 * @author    Shawn Rice <zooley@purdue.edu>
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
function CoursesBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['gid'])) 
	{
		$segments[] = $query['gid'];
		unset($query['gid']);
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
	if (!empty($query['roomid'])) 
	{
		$segments[] = $query['roomid'];
		unset($query['roomid']);
	}
	return $segments;
}

/**
 * Parse a SEF route
 * 
 * @param  array $segments Exploded route
 * @return array 
 */
function CoursesParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	if ($segments[0] == 'new' || $segments[0] == 'browse') 
	{
		$vars['task'] = $segments[0];
	} 
	else 
	{
		$vars['gid'] = $segments[0];
	}
	if (isset($segments[1])) 
	{
		switch ($segments[1])
		{
			case 'edit':
			case 'delete':
			case 'join':
			case 'accept':
			case 'cancel':
			case 'invite':
			case 'customize':
			case 'managepages':
			case 'editoutline':
			case 'managemodules':
			case 'ajaxupload':
				$vars['task'] = $segments[1];
			break;
			default:
				$vars['active'] = $segments[1];
			break;
		}
	}
	if (isset($segments[2])) 
	{
		if ($segments[1] == 'wiki') 
		{
			if (preg_match('/File:|Image:/', $segments[3])) 
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
		elseif ($segments[1] == 'chat') 
		{
			$vars['roomid'] = $segments[2];
		} 
		else 
		{
			$vars['task'] = $segments[2];
		}
	}

	return $vars;
}

