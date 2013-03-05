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
function GroupsBuildRoute(&$query)
{
	$segments = array();
	
	if (!empty($query['task']) && $query['task'] == 'view') 
	{
		unset($query['task']);
	}
	
	if (!empty($query['cn'])) 
	{
		$segments[] = $query['cn'];
		unset($query['cn']);
	}
	
	if(!empty($query['gid']))
	{
		//get the application object
		$application =& JFactory::getApplication();
		
		//check to see if we were already warned
		$alreadyWarned = JRequest::getBOOL('warned', 0);
		
		//if we were already warned dont redirect again.
		if (!$alreadyWarned)
		{
			//enqueue warning message of depreciated gid=
			$application->enqueueMessage('The component you are viewing is using depreciated methods to build group URL\'s. If you are the developer please fix or contact a system administrator with help resolving the issue.', 'warning');
			
			//redirect back to where user was going - needeed to do this to get message to show
			$redirect = $_SERVER['REQUEST_URI'];
			$redirect .= (strstr($redirect, "?") === false) ? '?warned=1' : '&warned=1';
			
			//redirect user
			$application->redirect( $redirect );
			return;
		}
		
		$segments[] = $query['gid'];
		unset($query['gid']);
	}
	
	if (!empty($query['active'])) 
	{
		$segments[] = $query['active'];
		if ($query['active'] == '' && !empty($query['task'])) 
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
		unset($query['active']);
	} 
	else 
	{
		if ((empty($query['scope']) || $query['scope'] == '') && !empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
	}
	if (!empty($query['scope'])) 
	{
		$segments[] = $query['scope'];
		unset($query['scope']);
	}
	if (!empty($query['pagename'])) 
	{
		$segments[] = $query['pagename'];
		unset($query['pagename']);
	}
	
	return $segments;
}

/**
 * Parse a SEF route
 * 
 * @param  array $segments Exploded route
 * @return array 
 */
function GroupsParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
		return $vars;
	}

	if ($segments[0] == 'new' || $segments[0] == 'browse' || $segments[0] == 'features')
	{
		$vars['task'] = $segments[0];
	} 
	else 
	{
		$vars['task'] = 'view';
		$vars['cn'] = $segments[0];
	}
	
	if (isset($segments[1])) 
	{
		switch ($segments[1])
		{
			case 'edit':
			case 'delete':
			case 'customize':
				$vars['task'] = $segments[1];
				break;
			case 'invite':
			case 'accept':
			case 'cancel':
			case 'join':
			case 'request':
				$vars['task'] = $segments[1];
				$vars['controller'] = 'membership';
				break;
			case 'pages':
			case 'addpage':
			case 'editpage':
			case 'savepage':
			case 'activatepage':
			case 'deactivatepage':
			case 'uppage':
			case 'downpage':
				$vars['task'] = $segments[1];
				$vars['controller'] = 'pages';
				break;
			default:
				$vars['active'] = $segments[1];
		}
	}
	
	if (isset($segments[2])) 
	{
		if ($segments[1] == 'wiki') 
		{
			if (isset($segments[3]) && preg_match('/File:|Image:/', $segments[3])) 
			{
				$vars['pagename'] = $segments[2];
			} 
			else 
			{
				$vars['pagename'] = array_pop($segments);
			}

			$s = implode(DS,$segments);
			$vars['scope'] = $s;
		}
		else 
		{
			$vars['action'] = $segments[2];
		}
	}
	
	return $vars;
}

