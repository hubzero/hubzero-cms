<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2012 Purdue University. All rights reserved.
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
 * @package   HUBzero
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');

/**
 * System plugin for hubzero
 */
class plgSystemSupergroup extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @since 1.5
	 */
	public function __construct(& $subject) 
	{
		parent::__construct($subject, NULL);
	}
	
	/**
	 * Method that fires after before a super group displays a super group comonent
	 *
	 * @return   void
	 */
	public function onBeforeRenderSuperGroupComponent()
	{
		// get request options
		$option = JRequest::getCmd('option', '');
		$cn     = JRequest::getVar('cn', '');
		$active = JRequest::getVar('active', '');
		
		// make sure we in groups
		if ($option != 'com_groups')
		{
			return;
		}
		
		// load group object
		$group  = \Hubzero\User\Group::getInstance( $cn );
		
		// make sure we have all the needed stuff
		if (is_object($group) && $group->isSuperGroup() && isset($cn) && isset($active))
		{
			// get com_groups params to get upload path
			$groupParams     = JComponentHelper::getParams('com_groups');
			$uploadPath      = JPATH_ROOT . DS . trim($groupParams->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');
			$componentPath   = $uploadPath . DS . 'components';
			$componentRouter = $componentPath . DS . 'com_' . $active . DS . 'router.php';
	
			// if we have a router
			if (file_exists($componentRouter))
			{
				// include router
				require_once $componentRouter;
			
				// build function name
				$parseRouteFunction = ucfirst($active) . 'ParseRoute';
				$parseRouteFunction = str_replace(array("-", "."), "", $parseRouteFunction);
				
				// if we have a build route functions, run it
				if (function_exists($parseRouteFunction))
				{	
					// get current route and remove prefix
					$currentRoute = rtrim(JURI::getInstance()->getPath(), DS);
					$currentRoute = trim(str_replace('groups' . DS . $group->get('cn') . DS . $active, '', $currentRoute), DS);
			
					// split route into segements
					$segments = explode('/', $currentRoute);
					
					// run segments through parser
					$vars = $parseRouteFunction($segments);
					
					// set each var
					foreach ($vars as $key => $var)
					{
						JRequest::setVar($key, $var);
					}
				}
			}
		}
	}
	
	/**
	 * Method that fires after an SEF route is built
	 *
	 * @param    $uri    URI after route has been built
	 * @return   void
	 */
	public function onAfterBuildSefRoute($uri)
	{
		// get current uri
		$current = JURI::getInstance();
		
		// get the current segments
		$currentSegments = explode(DS, trim($current->getPath(), DS));
		
		// make sure were building within groups
		if (!isset($currentSegments[0]) || !isset($currentSegments[1]) || $currentSegments[0] != 'groups')
		{
			return;
		}
		
		// get option from uri
		$url         = $uri->toString();
		$url         = str_replace('index.php', '', $url);
		$urlSegments = explode(DS, trim($url, DS));
		
		// make sure this is not a group route.
		if (!isset($urlSegments[0]) || $urlSegments[0] == 'groups')
		{
			return;
		}
		
		// get query string
		$query = $uri->getQuery(true);
		
		// get request options
		$cn     = JRequest::getVar('cn', '');
		$active = JRequest::getVar('active', '');
		
		// load group object
		$group  = \Hubzero\User\Group::getInstance( $cn );
		
		// make sure we have all the needed stuff
		if (is_object($group) && $group->isSuperGroup() && isset($cn) && isset($active))
		{
			// get com_groups params to get upload path
			$groupParams     = JComponentHelper::getParams('com_groups');
			$uploadPath      = JPATH_ROOT . DS . trim($groupParams->get('uploadpath', '/site/groups'), DS) . DS . $group->get('gidNumber');
			$componentPath   = $uploadPath . DS . 'components';
			$componentRouter = $componentPath . DS . 'com_' . $active . DS . 'router.php';

			// make sure uri is a super group component
			if (!is_dir($componentPath . DS . 'com_' . $urlSegments[0]))
			{
				return;
			}

			// if we have a router
			if (file_exists($componentRouter))
			{
				// include router
				require_once $componentRouter;
		
				// build function name
				$buildRouteFunction = ucfirst($active) . 'BuildRoute';
				$buildRouteFunction = str_replace(array("-", "."), "", $buildRouteFunction);
				
				// if we have a build route functions, run it
				if (function_exists($buildRouteFunction))
				{
					// get segments from router
					$routeParts = $buildRouteFunction($query);
					
					// build result
					$routeResult = implode('/', $routeParts);
					$routeResult = DS . 'groups' . DS . $group->get('cn') . DS . $active . DS . $routeResult;
					
					// set the new uri path and query string
					$uri->setPath($routeResult);
					$uri->setQuery($query);
				}
			}
		}
	}
}