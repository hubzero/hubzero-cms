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

function WishlistBuildRoute(&$query)
{
    $segments = array();
	
	if (!empty($query['category'])) {
        $segments[] = $query['category'];
        unset($query['category']);
    }
	
	if (!empty($query['rid'])) {
        $segments[] = $query['rid'];
        unset($query['rid']);
    }
	
    if (!empty($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }
	
	if (!empty($query['task'])) {
		if ($query['task'] != 'wishlist') {			
        	$segments[] = $query['task'];
        }
		unset($query['task']);
    }
   
	if (!empty($query['wishid'])) {
        $segments[] = $query['wishid'];
        unset($query['wishid']);
    }
		
    return $segments;
}

function WishlistParseRoute($segments)
{
    $vars = array();

    // Count route segments
    $count = count($segments);

	if (empty($segments[0])) {
		// default to main wish list
		$vars['task'] = 'wishlist';
		$vars['rid'] = 1;
		$vars['category'] = 'general';
		return $vars;
	}

    if (intval($segments[0]) && empty($segments[1]))
    {
		// we have a specific list id requested
		$vars['task'] = 'wishlist';
        $vars['id'] = $segments[0];
        return $vars;
    }
	else if(!intval($segments[0]) && empty($segments[1])) {
		// some general task
		$vars['task'] = $segments[0];
		return $vars;
	}
	

 	if (!empty($segments[1]))
    {
		
		if (intval($segments[0])) { 
			
			// we have a specific list id requested
			$vars['id'] = $segments[0];
			$vars['task'] = $segments[1];
			if(!empty($segments[2])) {
				$vars['wishid'] = $segments[2];
			}
			
		}
		else { 
		
			if($segments[0]=='rateitem') {
				$vars['task'] = 'rateitem';
				$vars['id'] = $segments[1];
				return $vars;
			}
			
			else if($segments[0]=='saveplan') {
				$vars['task'] = 'saveplan';
				$vars['wishid'] = $segments[1];
				return $vars;
			}
			else if($segments[0]=='wish') {
				$vars['task'] = 'wish';
				$vars['wishid'] = $segments[1];
				return $vars;
			}
			
			
			else {
			
				// we got a category
				$vars['category'] = $segments[0];
				$vars['rid'] = $segments[1];
				
				if(!empty($segments[2])) {
					$vars['task'] = $segments[2];
				}
				if(!empty($segments[3])) {
					$vars['wishid'] = $segments[3];
				}
			}
			
		}
		
        return $vars;
    }
	
	
    
    return $vars;
}

?>
