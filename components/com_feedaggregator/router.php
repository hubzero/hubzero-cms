<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'FeedaggregatorBuildRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function FeedaggregatorBuildRoute(&$query)
{
	$segments = array();

	if(isset($query['task']) && isset($query['controller']))
	{
		if (($query['task'] == 'new') && ($query['controller'] == 'feeds'))
		{
			$segments[0] = 'AddFeed';
			unset($query['task']);
			unset($query['controller']);
		}
		else if (($query['task'] == 'generateFeed') && ($query['controller'] == 'posts'))
		{
			$segments[0] = 'feed.rss';
			
			unset($query['task']);
			unset($query['controller']);
		} 
	} 
	else if (isset($query['controller']))
	{
		if($query['controller'] == 'feeds')
		{
			$segments[0] = 'feeds';
			unset($query['controller']);
		}
	}
	
	return $segments;
}

/**
 * Short description for 'FeedaggregatorParseRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function FeedaggregatorParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	if(isset($segments[0]))
	{
		switch($segments[0])
		{
			case 'RetrieveNewPosts':
				$vars['controller'] = 'posts';
				$vars['task'] = 'RetrieveNewPosts';
			break;
			case 'AddFeed':
				$vars['controller'] = 'feeds';
				$vars['task'] = 'new';
			break;
			case 'feed.rss':
				$vars['controller'] = 'posts';
				$vars['task'] = 'generateFeed';
			break;
			case 'feeds':
				$vars['controller'] = 'feeds';
				$vars['task'] = 'display';
		} // end switch	
	}
	
	return $vars;
}

