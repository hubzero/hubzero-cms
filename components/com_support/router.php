<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

function supportBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['view']) && strncmp($query['view'],'article',7) == 0 ) {
		unset( $query['view'] );
		unset( $query['id'] );
	}

	if (!empty($query['task'])) {
		switch ($query['task']) 
		{
			case 'stats':
			case 'tickets':
			case 'reportabuse':
				$segments[] = $query['task'];
				unset($query['task']);
			break;
			
			case 'ticket':
				if (!empty($query['id'])) {
					$segments[] = 'ticket';
					$segments[] = $query['id'];
					unset($query['task']);
					unset($query['id']);
				}
			break;
			
			case 'delete':
				if (!empty($query['id'])) {
					$segments[] = 'delete';
					$segments[] = $query['id'];
					unset($query['task']);
					unset($query['id']);
				}
			break;
			
			case 'feed':
				$segments[] = 'tickets';
				$segments[] = 'feed';
				unset($query['task']);
			break;
		}
	}

	return $segments;
}

function supportParseRoute($segments)
{
	$vars = array();

	$count = count($segments);

	if ($count == 0) {
		$vars['option'] = 'com_support';
		$vars['view'] = '';
		$vars['task'] = '';
		return $vars;
	}
	
	switch ($segments[0])
	{
		case 'report_problems':
	    	$vars['option'] = 'com_feedback';
	    	$vars['task'] = 'report';
		break;
		
		case 'tickets':
			if (isset($segments[1])) {
				$vars['task'] = 'feed';
				$vars['no_html'] = 1;
				$_GET['no_html'] = 1;
			} else {
				$vars['task'] = 'tickets';
			}
		break;
		
		case 'ticket':
		case 'delete':
		case 'reportabuse':
		default:
			$vars['task'] = (isset($segments[0])) ? $segments[0] : '';

			if (!empty($segments[1])) {
				$vars['id'] = $segments[1];
			}
		break;
	}

	return $vars;
}

