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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Build a SEF route from querystring vars
 *
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function StoreBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['controller']))
	{
		unset($query['controller']);
	}
	if (!empty($query['action']))
	{
		$segments[] = $query['action'];
		unset($query['action']);
	}
	if (!empty($query['item']))
	{
		$segments[] = $query['item'];
		unset($query['item']);
	}

	return $segments;
}

/**
 * Turn a SEF route into querystring vars
 *
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function StoreParseRoute($segments)
{
	$vars = array();

	if (empty($segments[0]))
	{
		return $vars;
	}

	if (isset($segments[0]))
	{
		$vars['task'] = $segments[0];
	}
	if (isset($segments[1]))
	{
		$vars['action'] = $segments[1];
	}
	if (isset($segments[2]))
	{
		$vars['item'] = $segments[2];
	}

	return $vars;
}

