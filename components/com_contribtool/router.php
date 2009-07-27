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

function ContribtoolBuildRoute(&$query)
{
    $segments = array();

   if (!empty($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
   if (!empty($query['toolid'])) {
		$segments[] = $query['toolid'];
		unset($query['toolid']);
	}
    if (!empty($query['alias'])) {
		$segments[] = $query['alias'];
		unset($query['alias']);
	}
		
	if (!empty($query['rid'])) {
		$segments[] = $query['rid'];
		unset($query['rid']);
	}
	if (!empty($query['step'])) {
		$segments[] = $query['step'];
		unset($query['step']);
	}
	

    return $segments;
}

function ContribtoolParseRoute($segments)
{
    $vars = array();

    // Count route segments
    $count = count($segments);

	if (empty($segments[0]))
		return $vars;

    if ($segments[0] == 'status')
    {
		if (empty($segments[1]))
			return $vars;

		$vars['task'] = 'status';

		if (!is_numeric($segments[1])) {
			$vars['alias'] = $segments[1];
			return $vars;
		}

        $vars['toolid'] = $segments[1];
        return $vars;
    }
	
	if ($segments[0] == 'create')
	{
		$vars['task'] = 'create';
		return $vars;
	}
	if ($segments[0] == 'edit')
	{
		$vars['task'] = 'edit';
		$vars['toolid'] = $segments[1];
		return $vars;
	}
	if ($segments[0] == 'cancel')
	{
		$vars['task'] = 'cancel';
		$vars['toolid'] = $segments[1];
		return $vars;
	}
	if ($segments[0] == 'versions')
	{
		$vars['task'] = 'versions';
		$vars['toolid'] = $segments[1];
		return $vars;
	}
	if ($segments[0] == 'pipeline')
	{
		$vars['task'] = 'pipeline';
		return $vars;
	}
	if ($segments[0] == 'start')
	{
		$vars['task'] = 'start';
		$vars['rid'] = $segments[1];
		$vars['step']  = $segments[2];
		return $vars;
	}
	if ($segments[0] == 'license')
	{
		$vars['task'] = 'license';
		$vars['toolid'] = $segments[1];
		return $vars;
	}
	if ($segments[0] == 'preview')
	{
		$vars['task'] = 'preview';
		$vars['rid'] = $segments[1];
		return $vars;
	}
	if ($segments[0] == 'deleteattach')
	{
		$vars['task'] = 'deleteattach';
		return $vars;
	}
	if ($segments[0] == 'saveattach')
	{
		$vars['task'] = 'saveattach';
		return $vars;
	}
	if ($segments[0] == 'orderupa')
	{
		$vars['task'] = 'orderupa';
		return $vars;
	}
	if ($segments[0] == 'orderdowna')
	{
		$vars['task'] = 'orderdowna';
		return $vars;
	}
	if ($segments[0] == 'removeauthor')
	{
		$vars['task'] = 'removeauthor';
		return $vars;
	}
	if ($segments[0] == 'saveauthor')
	{
		$vars['task'] = 'saveauthor';
		return $vars;
	}
	if ($segments[0] == 'orderupc')
	{
		$vars['task'] = 'orderupc';
		return $vars;
	}
	if ($segments[0] == 'orderdownc')
	{
		$vars['task'] = 'orderdownc';
		return $vars;
	}
	if ($segments[0] == 'movess')
	{
		$vars['task'] = 'movess';
		return $vars;
	}
	
	
    return $vars;
}

?>
