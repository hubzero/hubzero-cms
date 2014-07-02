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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'BlogBuildRoute'
 *
 * Long description (if any) ...
 *
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function BlogBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['task']) && $query['task'] != 'feed.rss' && $query['task'] != 'feed')
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['controller']))
	{
		if ($query['controller'] == 'media')
		{
			$segments[] = $query['controller'];
		}
		unset($query['controller']);
	}
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
	if (!empty($query['task']) && ($query['task'] == 'feed.rss' || $query['task'] == 'feed'))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['alias']))
	{
		$segments[] = $query['alias'];
		unset($query['alias']);
	}
	if (!empty($query['action']))
	{
		$segments[] = $query['action'];
		unset($query['action']);
	}

	return $segments;
}

/**
 * Short description for 'BlogParseRoute'
 *
 * Long description (if any) ...
 *
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function BlogParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	if (isset($segments[0]))
	{
		if (is_numeric($segments[0]))
		{
			$vars['year'] = $segments[0];
			$vars['task'] = 'browse';
		}
		else
		{
			if ($segments[0] == 'media')
			{
				$vars['controller'] = $segments[0];
			}
			$vars['task'] = $segments[0];
		}
	}
	if (isset($segments[1]))
	{
		if ($segments[1] == 'feed.rss')
		{
			$vars['task'] = 'feed';
		}
		else
		{
			$vars['month'] = $segments[1];
		}
	}
	if (isset($segments[2]))
	{
		if ($segments[2] == 'feed.rss')
		{
			$vars['task'] = 'feed';
		}
		else
		{
			$vars['alias'] = $segments[2];
			$vars['task'] = 'entry';
		}
	}
	if (isset($segments[3]))
	{
		if ($segments[2] == 'feed.rss')
		{
			$vars['task'] = 'feed';
		}
		else if ($segments[3] == 'comments.rss')
		{
			$vars['task'] = 'comments';
		}
		else if (strstr($segments[3], ':'))
		{
			$parts = explode(':', $segments[3]);
			$namespace = strtolower(trim($parts[0]));
			if (in_array($namespace, array('image', 'file')))
			{
				$vars['task'] = 'download';
				$vars['file'] = trim($parts[1]);
			}
		}
		else if ($segments[3] == 'editcomment')
		{
			$vars['action'] = $segments[3];
		}
		else
		{
			$vars['task'] = $segments[3];
		}
	}
	if (in_array($vars['task'], array('deletefile', 'deletefolder', 'upload', 'download')))
	{
		$vars['controller'] = 'media';
	}

	return $vars;
}

