<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Turn querystring parameters into an SEF route
 * 
 * @param  array &$query Querystring
 */
function HelpBuildRoute(&$query)
{
	$segments = array();
	
	//do we have a component
	if (!empty($query['component'])) 
	{
		$segments[] = $query['component'];
		unset($query['component']);
	}
	
	//do we have an extension
	if (!empty($query['extension'])) 
	{
		$segments[] = $query['extension'];
		unset($query['extension']);
	}
	
	//do we have a page
	if (!empty($query['page'])) 
	{
		$segments[] = $query['page'];
		unset($query['page']);
	}
	
	return $segments;
}

/**
 * Parse a SEF route
 * 
 * @param  array $segments Exploded route
 * @return array 
 */
function HelpParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}
	
	//do we have a component
	if (isset($segments[0]))
	{
		$vars['component'] = 'com_' . $segments[0];
	}
	
	//if we have segements it easy
	if (count($segments) > 2)
	{
		$vars['extension'] = $segments[1];
		$vars['page']      = $segments[2];
	}
	elseif (isset($segments[1]))
	{
		$vars['page'] = $segments[1];
	}
	
	return $vars;
}

