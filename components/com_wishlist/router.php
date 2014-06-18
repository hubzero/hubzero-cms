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
 * @author	Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Turn querystring parameters into an SEF route
 *
 * @param  array &$query Query string values
 * @return array Segments to build SEF route
 */
function WishlistBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['category']))
	{
		$segments[] = $query['category'];
		unset($query['category']);
	}

	if (!empty($query['rid']))
	{
		$segments[] = $query['rid'];
		unset($query['rid']);
	}

	if (!empty($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}

	if (!empty($query['task']))
	{
		if ($query['task'] != 'wishlist')
		{
			$segments[] = $query['task'];
		}
		unset($query['task']);
	}

	if (!empty($query['wishid']))
	{
		$segments[] = $query['wishid'];
		unset($query['wishid']);
	}

	if (!empty($query['file']))
	{
		$segments[] = $query['file'];
		unset($query['file']);
	}

	return $segments;
}

/**
 * Parse a SEF route
 *
 * @param  array $segments Exploded route segments
 * @return array
 */
function WishlistParseRoute($segments)
{
	$vars = array();

	// Count route segments
	$count = count($segments);

	if (empty($segments[0]))
	{
		// default to main wish list
		$vars['task'] = 'wishlist';
		$vars['rid'] = 1;
		$vars['category'] = 'general';
		return $vars;
	}

	if (intval($segments[0]) && empty($segments[1]))
	{
		// we have a specific list id requested
		$vars['task'] = 'wishlist';
		$vars['id'] = $segments[0];
		return $vars;
	}
	else if (!intval($segments[0]) && empty($segments[1]))
	{
		// some general task
		$vars['task'] = $segments[0];
		return $vars;
	}

 	if (!empty($segments[1]))
	{
		if (intval($segments[0]))
		{
			// we have a specific list id requested
			$vars['id'] = $segments[0];
			$vars['task'] = $segments[1];
			if (!empty($segments[2]))
			{
				$vars['wishid'] = $segments[2];
				if (!empty($segments[3]))
				{
					$vars['file'] = $segments[3];
					$vars['task'] = 'download';
				}
			}
		}
		else
		{
			switch ($segments[0])
			{
				case 'rateitem':
					$vars['task'] = 'rateitem';
					$vars['id'] = $segments[1];
					return $vars;
				break;
				case 'saveplan':
					$vars['task'] = 'saveplan';
					$vars['wishid'] = $segments[1];
					return $vars;
				break;
				case 'wish':
					$vars['task'] = 'wish';
					$vars['wishid'] = $segments[1];
					return $vars;
				break;
				default:
					// we got a category
					$vars['category'] = $segments[0];
					$vars['rid'] = $segments[1];

					if (!empty($segments[2]))
					{
						$vars['task'] = $segments[2];
					}
					if (!empty($segments[3]))
					{
						$vars['wishid'] = $segments[3];
					}
					if (!empty($segments[4]))
					{
						$vars['file'] = $segments[4];
						$vars['task'] = 'download';
					}
				break;
			}
		}

		return $vars;
	}

	return $vars;
}
