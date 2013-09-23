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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Projects build route
 * 
 * @param  array &$query
 * @return array Return
 */
function ProjectsBuildRoute(&$query)
{
	$segments = array();
	$scope = 0;

    if (!empty($query['alias'])) 
	{
		$segments[] = $query['alias'];
		unset($query['alias']);
	}
	if (!empty($query['id'])) 
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (!empty($query['task'])) 
	{
		if (empty($query['scope'])) 
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
	}
	if (!empty($query['active']))
	{
		$segments[] = $query['active'];
		unset($query['active']);
	}
	if (!empty($query['pid'])) 
	{
		$segments[] = $query['pid'];
		unset($query['pid']);
	}
	if (!empty($query['tool'])) 
	{
		$segments[] = $query['tool'];
		unset($query['tool']);
	}
	if (!empty($query['scope'])) 
	{
		// For wiki routing
		$segments = array();
		$scope = 1;
		$parts = explode ( '/', $query['scope'] );
		if(count($parts) >= 3) 
		{
			$segments[] = $parts[1]; // alias
			$segments[] = 'notes'; // active
			
			for( $i = 3; $i < count($parts); $i++ ) 
			{
				$segments[] = $parts[$i]; // inlcude parent page names
			}
		}
        unset($query['scope']);
    }
	if (!empty($query['pagename'])) 
	{
		$segments[] = $query['pagename'];
        unset($query['pagename']);
    }
	if (!empty($query['action'])) 
	{
		$segments[] = $query['action'];
		unset($query['action']);
	} 
	elseif ($scope == 1) 
	{
		$segments[] = !empty($query['task']) ? $query['task'] : 'view'; // wiki action	
		if(!empty($query['task'])) 
		{ 
			unset($query['task']);
		}
	}
	return $segments;
}

/**
 * Projects parse route
 * 
 * @param  array $segments
 * @return array Return
 */
function ProjectsParseRoute($segments)
{
	$vars  = array();
	
	// Valid tasks
	$tasks = array(	'start', 'setup', 'edit', 
		'browse', 'intro', 'features', 'auth',
		'deleteimg', 'wikipreview', 'fixownership', 
		'stats', 'reports', 'get'
	);
	
	// Views (plugins or view panels)
	$views = array('feed', 'info', 'team', 
		'files', 'tools', 'publications', 
		'notes', 'todo', 'activity', 'databases'
	);
	
	// Wiki actions
	$wiki_actions = array('media', 'list', 'upload', 
		'deletefolder', 'deletefile', 'view', 
		'new', 'edit', 'save', 'cancel', 
		'delete', 'deleteversion', 'approve', 
		'rename', 'saverename', 'history', 
		'compare', 'comments', 'editcomment', 
		'addcomment', 'savecomment', 'removecomment', 
		'reportcomment', 'deleterevision' 
	);
	
	// App actions
	$app_actions = array('status', 'history', 'wiki', 'browse', 
		'edit', 'start', 'save', 'register', 'attach', 'source',
		'cancel', 'update', 'message'
	);
		
	if (empty($segments[0]))
	{
		return $vars;
	}
		
	// Id?
	if (is_numeric($segments[0])) 
	{
		$vars['id'] = $segments[0];

		if (empty($segments[1])) 
		{
			$vars['task'] = 'view';
			return $vars;
		}	
	}

	// Alias?
	if (!is_numeric($segments[0])) 
	{
		if (in_array($segments[0], $tasks)) 
		{
			$vars['task'] = $segments[0];
			return $vars;
		}
		else 
		{
			$vars['alias']  = $segments[0];
		}
	}
	
	if (!empty($segments[1])) 
	{
		// Plugin?
		if (in_array($segments[1], $views)) 
		{
			$vars['active'] = $segments[1];
			$vars['task'] = 'view';
			
			// Publications
			if (!empty($segments[2]) && $vars['active'] == 'publications') 
			{
				if (is_numeric($segments[2])) 
				{
					$vars['pid'] = $segments[2];
					if (!empty($segments[3])) 
					{
						$vars['action'] = $segments[3];
					}
				}
				else 
				{
					$vars['action'] = $segments[2];
				}
			}
			
			// Apps
			if (!empty($segments[2]) && $vars['active'] == 'tools') 
			{
				if (in_array( $segments[2], $app_actions )) 
				{
					$vars['action'] = $segments[2];	
				}
				else 
				{
					$vars['tool'] = $segments[2];	
				}
				if (!empty($segments[3]) && in_array( $segments[3], $app_actions )) 
				{
					$vars['action'] = $segments[3];
				}
			}
			
			// Notes
			elseif (!empty($segments[2]) && !is_numeric($segments[2]) && $vars['active'] == 'notes') 
			{				
				$remaining = array_slice($segments, 2);
				$action = array_pop($remaining);
				$pagename = '';
				
				if (in_array( $action, $wiki_actions )) 
				{
					$vars['action'] = $action;	
					$pagename = array_pop($remaining);
				}
				else 
				{
					$vars['action'] = 'view';	
					$pagename = $action;
				}
				$vars['pagename'] = $pagename;
								
				// Collect scope
				if (isset($vars['alias']))
				{
					if (count($remaining) > 0) 
					{
						$scope = 'projects' . DS . $vars['alias'] . DS . 'notes';

						for ( $i = 0; $i < count($remaining); $i++ ) 
						{
							$scope .= DS . $remaining[$i]; // inlcude parent page names
						}
						if ($vars['action'] == 'new')
						{
							$scope .= DS . $pagename;
						}
						$vars['scope'] = $scope;
					}
					elseif ($vars['action'] == 'new')
					{
						$scope = 'projects' . DS . $vars['alias'] . DS . 'notes' . DS . $pagename;
						$vars['scope'] = $scope;
					}	
				}
				
				return $vars;				
			}
			// All other plugins
			elseif (!empty($segments[2]) && !is_numeric($segments[2])) 
			{
				$vars['action'] = $segments[2];
			}
			return $vars;
		}
	
		$vars['task'] = $segments[1];
		if(!empty($segments[2])) {
			$vars['active'] = $segments[2];
		}
	}
	else {
		$vars['task'] = 'view';
	}

	return $vars;
}

?>