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

function GroupsBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['gid'])) {
		$segments[] = $query['gid'];
		unset($query['gid']);
	}
	if (!empty($query['active'])) {
		$segments[] = $query['active'];
		if ($query['active'] == '' && !empty($query['task'])) {
			$segments[] = $query['task'];
			unset($query['task']);
		}
		unset($query['active']);
	} else {
		if ((empty($query['scope']) || $query['scope'] == '') && !empty($query['task'])) {
			$segments[] = $query['task'];
			unset($query['task']);
		}
	}
	if (!empty($query['scope'])) {
        $segments[] = $query['scope'];
        unset($query['scope']);
    }
	if (!empty($query['pagename'])) {
        $segments[] = $query['pagename'];
        unset($query['pagename']);
    }
 
    return $segments;
}

function GroupsParseRoute($segments)
{
    $vars = array();

    if (empty($segments))
    	return $vars;

    if ($segments[0] == 'new' || $segments[0] == 'browse') {
		$vars['task'] = $segments[0];
	} else {
		$vars['gid'] = $segments[0];
	}
	if (isset($segments[1])) {
		switch ($segments[1]) 
		{
			case 'edit':
			case 'delete':
			case 'join':
			case 'accept':
			case 'cancel':
			case 'invite':
				$vars['task'] = $segments[1];
			break;
			default:
				$vars['active'] = $segments[1];
			break;
		}
	}
	if (isset($segments[2])) {
		if ($segments[1] == 'wiki') {
			$vars['pagename'] = array_pop($segments);
			$s = implode(DS,$segments);
			$vars['scope'] = $s;
		} else {
			$vars['task'] = $segments[2];
		}
	}

    return $vars;
}
