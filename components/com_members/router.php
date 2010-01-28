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

function membersBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['id'])) {
		if (substr($query['id'],0,1) == '-') {
			$query['id'] = 'n' . substr($query['id'],1);
		}
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (!empty($query['active'])) {
		$segments[] = $query['active'];
		unset($query['active']);
		
		if (!empty($query['task'])) {
			$segments[] = $query['task'];
			unset($query['task']);
		}
	}
	if (empty($query['id']) && !empty($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	/**/
 
    return $segments;
}

function membersParseRoute($segments)
{
	$vars = array();

	if (empty($segments)) {
		return $vars;
	}

	if (isset($segments[0])) {
		if ($segments[0] == 'whois' || $segments[0] == 'activity') {
			$vars['task'] = $segments[0];
		} elseif ($segments[0]{0} == 'n') {
			$vars['id'] = '-' . substr($segments[0],1);
		} else {
			$vars['id'] = $segments[0];
		}
	}
	if (isset($segments[1])) {
		if ($segments[1] == 'edit' || $segments[1] == 'changepassword' || $segments[1] == 'raiselimit' || $segments[1] == 'cancel' || $segments[1] == 'deleteimg') {
			$vars['task'] = $segments[1];
		} else {
			$vars['active'] = $segments[1];

			if (isset($segments[2])) {
				if (trim($segments[1]) == 'profile') {
					$vars['task'] = $segments[2];
				} else {
					$vars['action'] = $segments[2];
				}
			}
		}
	}
	/**/

	return $vars;
}

?>
