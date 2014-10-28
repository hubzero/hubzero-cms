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
 * Publications build route
 *
 * @param  array &$query
 * @return array Return
 */
function PublicationsBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['controller']))
	{
		$segments[] = $query['controller'];
		unset($query['controller']);
	}
	if (!empty($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (!empty($query['alias']))
	{
		$segments[] = $query['alias'];
		unset($query['alias']);
	}
	if (!empty($query['active']))
	{
		$segments[] = $query['active'];
		unset($query['active']);
	}
	if (!empty($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['category']))
	{
		$segments[] = $query['category'];
		unset($query['category']);
	}
	if (!empty($query['pid']))
	{
		$segments[] = $query['pid'];
		unset($query['pid']);
	}
	if (!empty($query['v']))
	{
		$segments[] = $query['v'];
		unset($query['v']);
	}
	if (!empty($query['a']))
	{
		$segments[] = $query['a'];
		unset($query['a']);
	}
	if (!empty($query['file']))
	{
		$segments[] = $query['file'];
		unset($query['file']);
	}

	return $segments;
}

/**
 * Publications parse route
 *
 * @param  array $segments
 * @return array Return
 */
function PublicationsParseRoute($segments)
{
	$vars = array();
	$vars['controller'] = 'publications';

	// Valid tasks not requiring id
	$tasks = array(	'browse', 'start', 'submit', 'edit', 'publication');

	if (empty($segments[0]))
	{
		return $vars;
	}

	if (!empty($segments[0]) && $segments[0] == 'curation')
	{
		$vars['controller'] = 'curation';

		if (!empty($segments[1]) && is_numeric($segments[1]))
		{
			$vars['id']   = $segments[1];

			if (!empty($segments[2]))
			{
				$vars['task'] = $segments[2];
			}
			else
			{
				$vars['task'] = 'view';
			}
		}

		return $vars;
	}

	if (is_numeric($segments[0]))
	{
		$vars['task'] = 'view';
		$vars['id']   = $segments[0];
	}
	elseif (in_array($segments[0], $tasks))
	{
		$vars['task'] = $segments[0];
		if (!empty($segments[1]))
		{
			if (is_numeric($segments[1]))
			{
				$vars['pid'] = $segments[1];
			}
		}
	}
	else
	{
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS
			. 'com_publications' . DS . 'tables' . DS . 'category.php');

		$database = JFactory::getDBO();

		$t = new PublicationCategory( $database );
		$cats = $t->getCategories();

		foreach ($cats as $cat)
		{
			if (trim($segments[0]) == $cat->url_alias)
			{
				$vars['category'] = $segments[0];
				$vars['task'] = 'browse';
			}
		}

		if (!isset($vars['category']))
		{
			$vars['alias'] = $segments[0];
		}
	}

	if (!empty($segments[1]))
	{
		switch ($segments[1])
		{
			case 'edit':
				$vars['task'] = 'edit';
				if (is_numeric($segments[0]))
				{
					$vars['pid'] = $segments[0];
					$vars['id']  = '';
				}
				break;

			case 'download':
			case 'wiki':
			case 'play':
			case 'watch':
			case 'serve':
			case 'video':
				$vars['task'] = $segments[1];

				if (!empty($segments[2]))
				{
					$vars['v'] = $segments[2];
				}
				if (!empty($segments[3]))
				{
					$vars['a'] = $segments[3];
				}

				break;

			case 'citation': $vars['task'] = 'citation'; break;
			case 'feed.rss': $vars['task'] = 'feed';     break;
			case 'feed':     $vars['task'] = 'feed';     break;
			case 'license':  $vars['task'] = 'license';  break;

			default:
				if ($segments[0] == 'browse')
				{
					$vars['category'] = $segments[1];
				}
				else
				{
					$vars['active'] = $segments[1];
				}
			break;
		}
	}

	return $vars;
}
?>
