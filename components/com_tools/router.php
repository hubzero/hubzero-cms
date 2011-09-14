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

	/*if (!empty($query['invoke'])) {
		$segments[] = 'invoke';
		$segments[] = $query['invoke'];
		unset($query['invoke']);
	}*/
	if (!empty($query['app'])) {
		$segments[] = $query['app'];
		unset($query['app']);
	}
	if (!empty($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (!empty($query['version'])) {
		$segments[] = $query['version'];
		unset($query['version']);
	}
	if (isset($query['sess'])) {
		$segments[] = $query['sess'];
		unset($query['sess']);
	}
	if (isset($query['return']) && $query['return'] == '') {
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

	if (empty($segments)) {
		return $vars;
	}

	if (isset($segments[0])) {
		switch ($segments[0])
		{
			case 'login':
			case 'accessdenied':
			case 'quotaexceeded':
			case 'storageexceeded':
			case 'storage':
			case 'rename':
			case 'diskusage':
			case 'purge':
			//case 'share':
			//case 'unshare':
			//case 'invoke':
			//case 'view':
			//case 'stop':
			case 'images':
			case 'listfiles':
			case 'download':
			case 'deletefolder':
			case 'deletefile':
				$vars['task'] = $segments[0];
			break;

			default:
				$vars['option'] = 'com_resources';
				$vars['alias'] = $segments[0];
			break;
		}
	}
	if (isset($segments[1])) {
		switch ($segments[1])
		{
			case 'invoke':
				$vars['option'] = 'com_tools';
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
				$vars['app'] = $segments[0];
				$vars['task'] = $segments[1];
				if (isset($segments[2])) {
					$vars['sess'] = $segments[2];
				}
			break;
			case 'report':
				$xhub =& Hubzero_Factory::getHub();
				$xhub->redirect(JRoute::_('index.php?option=com_support&task=tickets&find=group:app-' . $segments[0]));
			break;
			case 'forge.png':
				$vars['task'] = 'image';
			break;
			case 'site_css.cs':
				$vars['task'] = 'css';
			break;
			default:
				$vars['sess'] = $segments[1];
			break;
		}
		/*switch ($segments[1]) 
		{
			case 'accessdenied':
			case 'share':
			case 'unshare':
			case 'invoke':
			case 'view':
			case 'stop':
				$vars['option'] = 'com_tools';
				$vars['task'] = $segments[1];
			break;
			
			default:
				$vars['option'] = 'com_resources';
				$vars['alias'] = $segments[0];
			break;
		}*/
	}

	return $vars;
}

?>