<?php
/**
 * @package		HUBzero                                  CMS
 * @author		Shawn                                     Rice <zooley@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * 
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ForumBuildRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function ForumBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['section'])) {
        $segments[] = $query['section'];
        unset($query['section']);
    }
	if (!empty($query['category'])) {
        $segments[] = $query['category'];
        unset($query['category']);
    }
	if (!empty($query['thread'])) {
        $segments[] = $query['thread'];
        unset($query['thread']);
    }
	if (!empty($query['post'])) {
		$segments[] = $query['post'];
		unset($query['post']);
	}
	if (!empty($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['file'])) {
		$segments[] = $query['file'];
		unset($query['file']);
	}

    return $segments;
}

/**
 * Short description for 'ForumParseRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function ForumParseRoute($segments)
{
    $vars = array();

	if (empty($segments)) {
		return $vars;
	}

    if (isset($segments[0])) {
		$vars['controller'] = 'sections';
		$vars['task'] = 'display';
		$vars['section'] = $segments[0];
	}

	if (isset($segments[1])) {
		switch ($segments[1])
		{
			case 'new':
				$vars['task'] = $segments[1];
				$vars['controller'] = 'categories';
			break;
			
			case 'edit':
			case 'save':
			case 'delete':
				$vars['task'] = $segments[1];
				$vars['controller'] = 'sections';
			break;
			
			default:
				$vars['controller'] = 'categories';
				$vars['task'] = 'display';
				$vars['category'] = $segments[1];
			break;
		}
	}
	
	if (isset($segments[2])) {
		switch ($segments[2])
		{
			case 'new':
				$vars['task'] = $segments[2];
				$vars['controller'] = 'threads';
			break;
			
			case 'edit':
			case 'save':
			case 'delete':
				$vars['task'] = $segments[2];
				$vars['controller'] = 'categories';
			break;
			
			default:
				$vars['controller'] = 'threads';
				$vars['task'] = 'display';
				$vars['thread'] = $segments[2];
			break;
		}
	}
	
	if (isset($segments[3])) {
		switch ($segments[3])
		{
			case 'new':
				$vars['task'] = $segments[3];
				$vars['controller'] = 'threads';
			break;
			
			case 'edit':
			case 'save':
			case 'delete':
				$vars['task'] = $segments[3];
				$vars['controller'] = 'threads';
			break;
			
			default:
				$vars['controller'] = 'threads';
				$vars['task'] = 'display';
				$vars['post'] = $segments[3];
			break;
		}
	}
	
	if (isset($segments[4])) {
		$vars['task'] = 'download';
		$vars['file'] = $segments[4];
	}

    return $vars;
}
