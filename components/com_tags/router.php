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

function tagsBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['tag'])) {
		//$segments[] = 'view';
		$segments[] = $query['tag'];
		unset($query['tag']);
	}
	if (!empty($query['area'])) {
		$segments[] = $query['area'];
		unset($query['area']);
	}
	if (!empty($query['task'])) {
		if ($query['task'] != 'edit') {
			$segments[] = $query['task'];
			unset($query['task']);
		}
	}
	return $segments;
}

function tagsParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
		return $vars;

	if (isset($segments[0])) {
		if ($segments[0] == 'browse' || $segments[0] == 'delete' || $segments[0] == 'edit') {
			$vars['task'] = $segments[0];
		} else {
			$vars['tag']  = $segments[0];
			$vars['task'] = 'view';
		}
	}
	if (isset($segments[1])) {
		if ($segments[1] == 'feed' || $segments[1] == 'feed.rss') {
			$vars['task'] = $segments[1];
		} else {
			$vars['area'] = $segments[1];
		}
	}
	if (isset($segments[2])) {
		$vars['task'] = $segments[2];
	}

	return $vars;
}

?>