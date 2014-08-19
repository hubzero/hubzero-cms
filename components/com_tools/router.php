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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'toolsBuildRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array &$query Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function toolsBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['controller'])) 
	{
		if ($query['controller'] == 'media')
		{
			$segments[] = $query['controller'];
		}
		unset($query['controller']);
	}
	if (!empty($query['app'])) 
	{
		$segments[] = $query['app'];
		unset($query['app']);
	}
	if (!empty($query['task'])) 
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['version'])) 
	{
		$segments[] = $query['version'];
		unset($query['version']);
	}
	/*if (isset($query['sess'])) 
	{
		$segments[] = $query['sess'];
		unset($query['sess']);
	}*/
	if (isset($query['return']) && $query['return'] == '') 
	{
		unset($query['return']);
	}

	return $segments;
}

/**
 * Short description for 'toolsParseRoute'
 * 
 * Long description (if any) ...
 * 
 * @param  array $segments Parameter description (if any) ...
 * @return array Return description (if any) ...
 */
function toolsParseRoute($segments)
{
	$vars = array();

	if (empty($segments)) 
	{
		return $vars;
	}

	if (isset($segments[0])) 
	{
		switch ($segments[0])
		{
			case 'media':
				$vars['controller'] = 'media';
			break;

			case 'pipeline':
			case 'create':
				$vars['task'] = $segments[0];
				$vars['controller'] = 'pipeline';
			break;
			
			case 'login':
			case 'accessdenied':
			case 'quotaexceeded':
			case 'rename':
				$vars['task'] = $segments[0];
				$vars['controller'] = 'sessions';
			break;
			
			case 'assets':
				if (count($segments) < 3)
				{
					break;
				}
				$vars['task'] = 'assets';
				$vars['controller'] = 'tools';
     				$vars['type'] = $segments[1];
				$vars['file'] = $segments[2];
				return $vars;
			break;

			case 'images':
				$vars['task'] = $segments[0];
				$vars['controller'] = 'tools';
			break;
			
			case 'diskusage':
			case 'storageexceeded':
			case 'storage':
			case 'filelist':
			case 'deletefolder':
			case 'deletefile':
			case 'purge':
				$vars['task'] = $segments[0];
				$vars['controller'] = 'storage';
			break;

			case 'reorder':
			case 'delete':
			case 'save':
				$vars['option'] = 'com_tools';
				$vars['controller'] = 'attachments';
				$vars['task'] = $segments[0];
			break;

			default:
				// This is an alias
				// /tools/mytool => /resources/mytool
				$vars['option'] = 'com_resources';
				$vars['alias'] = $segments[0];
			break;
		}
	}

	if (isset($segments[1])) 
	{
		switch ($segments[1])
		{
			case 'delete':
				if (isset($vars['controller']) && $vars['controller'] == 'media')
				{
					$vars['task'] = $segments[1];
				}
			break;

			case 'publish':
			case 'install':
			case 'retire':
			case 'addrepo':
				$vars['option'] = 'com_tools';
				$vars['controller'] = 'admin';
				$vars['app'] = $segments[0];
				$vars['task'] = $segments[1];
			break;

			// Pipeline controller
			case 'register':
			case 'edit':
			case 'save':
			case 'update':
			case 'message':
			case 'cancel':
			case 'create':
			case 'versions':
			case 'saveversion':
			case 'finalizeversion':
			case 'license':
			case 'savelicense':
			case 'finalize':
			case 'releasenotes':
			case 'savenotes':
			case 'start':
			case 'wiki':
			case 'status':
				$vars['option'] = 'com_tools';
				$vars['controller'] = 'pipeline';
				$vars['app'] = $segments[0];
				$vars['task'] = $segments[1];
			break;

			// Resource controller
			case 'preview':
			case 'resource':
				$vars['option'] = 'com_tools';
				$vars['controller'] = 'resource';
				$vars['app'] = $segments[0];
				if ($segments[1] == 'preview')
				{
					$vars['task'] = $segments[1];
				}
			break;

			// Sessions controller
			case 'reinvoke':
			case 'invoke':
				$vars['option'] = 'com_tools';
				$vars['controller'] = 'sessions';
				$vars['app'] = $segments[0];
				$vars['task'] = $segments[1];
				if (isset($segments[2])) {
					$vars['version'] = $segments[2];
				}
			break;

			case 'session':
			case 'share':
			case 'unshare':
			case 'stop':
				$vars['option'] = 'com_tools';
				$vars['controller'] = 'sessions';
				$vars['app'] = $segments[0];
				if ($segments[1] == 'session')
				{
					$vars['task'] = 'view';
				}
				else
				{
					$vars['task'] = $segments[1];
				}
				if (isset($segments[2])) 
				{
					$vars['sess'] = $segments[2];
				}
			break;

			// Tools controller
			case 'report':
				JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_support&task=tickets&find=group:app-' . $segments[0]),'','message',true);
				exit();
			break;

			case 'forge.png':
				$vars['task'] = 'image';
				$vars['controller'] = 'tools';
			break;

			case 'site_css.cs':
			case 'site_css.css':
				$vars['task'] = 'css';
				$vars['controller'] = 'tools';
			break;

			default:
				$vars['sess'] = $segments[1];
			break;
		}
	}

	return $vars;
}
