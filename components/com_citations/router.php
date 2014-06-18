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
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function CitationsBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (!empty($query['format']))
	{
		$segments[] = $query['format'];
		unset($query['format']);
	}
	/*
	if (!empty($query['area']))
	{
		$segments[] = $query['area'];
		unset($query['area']);
	}
	*/
	return $segments;
}

/**
 * Parse a SEF route
 *
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function CitationsParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	if (isset($segments[0]))
	{
		$vars['task'] = $segments[0];
		switch ($vars['task'])
		{
			case 'import':
				$vars['controller'] = 'import';
				$vars['task'] = 'display';
			break;

			case 'import_upload':
			case 'import_review':
			case 'import_save':
			case 'import_saved':
				$vars['controller'] = 'import';
				$vars['task'] = str_replace('import_', '', $vars['task']);
			break;

			default:
				$vars['controller'] = 'citations';
			break;
		}
	}
	if (isset($segments[1]))
	{
		$vars['id'] = $segments[1];
		/*
		if (isset($segments[2]))
		{
			$vars['area'] = $segments[2];
		}
		*/
	}
	if (isset($segments[2]))
	{
		$vars['format'] = $segments[2];
	}
	return $vars;
}
