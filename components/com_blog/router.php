<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
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

function BlogBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['year'])) {
		$segments[] = $query['year'];
		unset($query['year']);
	}
	if (!empty($query['month'])) {
		$segments[] = $query['month'];
		unset($query['month']);
	}
	if (!empty($query['alias'])) {
		$segments[] = $query['alias'];
		unset($query['alias']);
	}
 
    return $segments;
}

function BlogParseRoute($segments)
{
	$vars = array();

    if (empty($segments))
    	return $vars;

    if (isset($segments[0])) {
		if (is_numeric($segments[0])) {
			$vars['year'] = $segments[0];
			$vars['task'] = 'browse';
		} else {
			$vars['task'] = $segments[0];
		}
	}
	if (isset($segments[1])) {
		$vars['month'] = $segments[1];
	}
	if (isset($segments[2])) {
		if ($segments[2] == 'feed.rss') {
			$vars['task'] = 'feed';
		} else {
			$vars['alias'] = $segments[2];
			$vars['task'] = 'entry';
		}
	}
	if (isset($segments[3])) {
		if ($segments[3] == 'comments.rss') {
			$vars['task'] = 'comments';
		}
	}

    return $vars;
}
