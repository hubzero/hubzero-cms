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

function ResourcesBuildRoute(&$query)
{
    $segments = array();

    if (!empty($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}
    if (!empty($query['alias'])) {
		$segments[] = $query['alias'];
		unset($query['alias']);
	}
	if (!empty($query['active'])) {
		$segments[] = $query['active'];
		unset($query['active']);
	}
	if (!empty($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['file'])) {
		$segments[] = $query['file'];
		unset($query['file']);
	}
	if (!empty($query['type'])) {
		$segments[] = $query['type'];
		unset($query['type']);
	}

    return $segments;
}

function ResourcesParseRoute($segments)
{
	$vars = array();

	if (empty($segments[0]))
		return $vars;

	if (is_numeric($segments[0])) {
		$vars['id'] = $segments[0];
	} else {
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php');
		
		$database =& JFactory::getDBO();
		
		$t = new ResourcesType( $database );
		$types = $t->getMajorTypes();
		
		// Normalize the title
		// This is so we can determine the type of resource to display from the URL
		// For example, /resources/learningmodules => Learning Modules
		$normalized_valid_chars = 'a-zA-Z0-9';
		for ($i = 0; $i < count($types); $i++) 
		{	
			$normalized = preg_replace("/[^$normalized_valid_chars]/", "", $types[$i]->type);
			$normalized = strtolower($normalized);
			//$types[$i]->title = $normalized;
			
			if (trim($segments[0]) == $normalized) {
				$vars['type'] = $segments[0];
				$vars['task'] = 'browsetags';
			}
		}
		
		if ($segments[0] == 'license') {
			$vars['task'] = $segments[0];
		} else {
			if (!isset($vars['type'])) {
				$vars['alias'] = $segments[0];
			}
		}
	}
		
	/*else if ($segments[0] == 'animations')
		$vars['type'] = '5';
	else if ($segments[0] == 'courses')
		$vars['type'] = '6';
	else if ($segments[0] == 'learningmodules')
		$vars['type'] = '4';
	else if ($segments[0] == 'notes')
		$vars['type'] = '10';
	else if ($segments[0] == 'downloads')
		$vars['type'] = '9';
	else if ($segments[0] == 'presentations' || $segments[0] == 'onlinepresentations')
		$vars['type'] = '1';
	else if ($segments[0] == 'publications')
		$vars['type'] = '3';
	else if ($segments[0] == 'series')
		$vars['type'] = '31';
	else if ($segments[0] == 'tags')
		$vars['task'] = 'tags';
	else if ($segments[0] == 'teachingmaterials')
		$vars['type'] = '39';
	else if ($segments[0] == 'tools')
		$vars['type'] = '7';
	else if ($segments[0] == 'toolsets')
		$vars['type'] = '8';
	else if ($segments[0] == 'workshops')
		$vars['type'] = '2';
	else if ($segments[0] == 'tutorials') {
		$vars['type'] = '1';
		$vars['tag'] = 'tutorial';
	} else if ($segments[0] == 'seminars') {
		$vars['type'] = '1';
		$vars['tag'] = 'researchseminar';
	} else {
		$vars['alias'] = $segments[0];
	}*/

	if (!empty($segments[1])) {
		switch ($segments[1]) 
		{
			case 'download': $vars['task'] = 'download'; break;
			case 'play':     $vars['task'] = 'play';     break;
			//case 'license':  $vars['task'] = 'license';  break;
			case 'citation': $vars['task'] = 'citation'; break;
			case 'feed.rss': $vars['task'] = 'feed';     break;
			case 'feed':     $vars['task'] = 'feed';     break;
			
			default: $vars['active'] = $segments[1]; break;
		}
	}

	return $vars;
}

?>
