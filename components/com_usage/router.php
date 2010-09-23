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

function UsageBuildRoute(&$query)
{
    $segments = array();

    if (!empty($query['task'])) {
        $segments[] = $query['task'];
        unset($query['task']);
    }
	if (!empty($query['period'])) {
        $segments[] = $query['period'];
        unset($query['period']);
    }
	if (!empty($query['type'])) {
        $segments[] = $query['type'];
        unset($query['type']);
    }
 
    return $segments;
}

function UsageParseRoute($segments)
{
    $vars = array();

    if (empty($segments))
    	return $vars;

    $vars['task'] = $segments[0];
	if (isset($segments[0])) {
		switch ($segments[0]) 
		{
			case 'maps': 
				if (isset($segments[1])) {
					$vars['type'] = $segments[1];
				}
			break;
			case 'overview':
			default:
				if (isset($segments[1])) {
					$vars['period'] = $segments[1];
				}
			break;
		}
	}
	
    return $vars;
}
