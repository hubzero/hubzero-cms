<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

function AnswersBuildRoute(&$query)
{
    $segments = array();

    if (!empty($query['task'])) {
		if ($query['task'] == 'new') {
			$segments[] = 'question';
			$segments[] = 'new';
		} else {
        	$segments[] = $query['task'];
        }
		unset($query['task']);
    }
    if (!empty($query['tag'])) {
        $segments[] = $query['tag'];
        unset($query['tag']);
    }
    if (!empty($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }
    return $segments;
}

function AnswersParseRoute($segments)
{
    $vars = array();

    // Count route segments
    $count = count($segments);

	if (empty($segments[0])) {
		return $vars;
	}

    switch ($segments[0])
	{
		case 'question':
			if (empty($segments[1])) {
				return $vars;
			}

			$vars['task'] = 'question';

			if ($segments[1] == 'new') {
				$vars['task'] = 'new';
				return $vars;
			}

	        $vars['id'] = $segments[1];
		break;
		
		case 'tags':
			$vars['task'] = 'tags';
        	$vars['tag'] = $segments[1];
		break;
		
		case 'myquestions':
			$vars['task'] = 'myquestions';
		break;
		
		case 'search':
			$vars['task'] = 'search';
		break;

		case 'answer':
			$vars['task'] = 'answer';
			$vars['id'] = $segments[1];
		break;
		
		case 'delete':
			$vars['task'] = 'delete';
			$vars['id'] = $segments[1];
		break;
		
		case 'delete_q': 
			$vars['task'] = 'delete_q';
			$vars['id'] = $segments[1];
		break;

		case 'rateitem':
			$vars['task'] = 'rateitem';
		break;

		case 'reply':
			$vars['task'] = 'reply';
			$vars['id'] = $segments[1];
		break;
		
		case 'math':
			$vars['task'] = 'math';
			$vars['id'] = $segments[1];
		break;
		
		case 'savereply':
			$vars['task'] = 'reply';
		break;
		
		case 'accept':
			$vars['task'] = 'accept';
			$vars['id'] = $segments[1];
			$vars['rid'] = $segments[2];
		break;
	}

    return $vars;
}
?>