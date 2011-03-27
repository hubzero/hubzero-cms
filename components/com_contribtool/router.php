<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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
		if (!empty($segments[1]))
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

