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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


function JobsBuildRoute(&$query)
{
    $segments = array();
	
	if (!empty($query['task'])) {
		if ($query['task'] != 'all') {			
        	$segments[] = $query['task'];
        }
		unset($query['task']);
    }
	
	if (!empty($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }
	
	if (!empty($query['code'])) {
        $segments[] = $query['code'];
        unset($query['code']);
    }
	
	if (!empty($query['employer'])) {
        $segments[] = $query['employer'];
        unset($query['employer']);
    }
    return $segments;
}

function JobsParseRoute($segments)
{
    $vars = array();

    // Count route segments
    $count = count($segments);

	if (empty($segments[0])) {
		// default to all jobs
		$vars['task'] = 'all';
		return $vars;
	}

    if(!intval($segments[0]) && empty($segments[1])) {
		// some general task
		$vars['task'] = $segments[0];
		return $vars;
	}
	
 	if (!empty($segments[1]))
    {		
			if($segments[0]=='job') {
				$vars['task'] = 'job';
				$vars['code'] = $segments[1];
				return $vars;
			}			
			else if($segments[0]=='editjob') {
				$vars['task'] = 'editjob';
				$vars['code'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='editresume') {
				$vars['task'] = 'editresume';
				$vars['id'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='apply') {
				$vars['task'] = 'apply';
				$vars['code'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='editapp') {
				$vars['task'] = 'editapp';
				$vars['code'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='withdraw') {
				$vars['task'] = 'withdraw';
				$vars['code'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='confirmjob') {
				$vars['task'] = 'confirmjob';
				$vars['code'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='unpublish') {
				$vars['task'] = 'unpublish';
				$vars['code'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='reopen') {
				$vars['task'] = 'reopen';
				$vars['code'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='remove') {
				$vars['task'] = 'remove';
				$vars['code'] = $segments[1];
				return $vars;
			}	
			else if($segments[0]=='browse' or $segments[0]=='all') {
				$vars['task'] = 'browse';
				$vars['employer'] = $segments[1];
				return $vars;
			}		
    }
	    
    return $vars;
}
?>