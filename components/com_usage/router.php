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
 * @param  array &$query Query string values
 * @return array Segments to build SEF route
 */
function UsageBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['period']))
	{
		$segments[] = $query['period'];
		unset($query['period']);
	}
	if (!empty($query['type']))
	{
		$segments[] = $query['type'];
		unset($query['type']);
	}

	return $segments;
}

/**
 * Parse a SEF route
 *
 * @param  array $segments Exploded route segments
 * @return array
 */
function UsageParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	$vars['task'] = $segments[0];
	if (isset($segments[0]))
	{
		switch ($segments[0])
		{
			case 'maps':
				if (isset($segments[1]))
				{
					$vars['type'] = $segments[1];
				}
			break;
			case 'overview':
			default:
				if (isset($segments[1]))
				{
					$vars['period'] = $segments[1];
				}
			break;
		}
	}

	return $vars;
}

