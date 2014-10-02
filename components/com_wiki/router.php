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
function WikiBuildRoute(&$query)
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
 * Parse a SEF route
 *
 * @param  array $segments Exploded route segments
 * @return array
 */
function WikiParseRoute($segments)
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
		$vars['task'] = JRequest::getWord('task', '');
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
