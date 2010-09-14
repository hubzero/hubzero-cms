<?php
/**
* @version		$Id: router.php 10752 2008-08-23 01:53:31Z eddieajau $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

function UserBuildRoute(&$query)
{
	$segments = array();

	if(isset($query['view']))
	{
		if(empty($query['Itemid'])) {
			$segments[] = $query['view'];
		} else {
			$menu = &JSite::getMenu();
			$menuItem = &$menu->getItem( $query['Itemid'] );
			if(!isset($menuItem->query['view']) || $menuItem->query['view'] != $query['view']) {
				$segments[] = $query['view'];
			}
		}
		unset($query['view']);
	}
	return $segments;
}

function UserParseRoute($segments)
{
	$vars = array();
	
	//Get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();

	// Count route segments
	$count = count($segments);
	
	if (isset($item)) {
		$vars = $item->query;
	}
	
	if (!empty($count) && !isset($vars['view'])) {
		$vars['view'] = $segments[0];
		array_shift($segments);
		$count--;
	}
	
	if ($vars['view'] == 'login') {
		if ($count > 0) {
			$vars['authenticator'] = array_shift($segments);
			$count--;
		}
		
		if ($count > 0) {
			$vars['domain'] = array_shift($segments);
			$count--;
		}
	
		$uri = JFactory::getURI();

		// 	if there are any query parameters other than return, then this must be a login task request
		
		if ($uri->getQuery() != "") 
		{		
			if (count($uri->_vars) > 1)
				$vars['task'] = 'login';
			else if (!isset($uri->_vars['return']))
				$vars['task'] = 'login';
		}
	}
	
	if ($count > 1) {
		$vars['id']    = $segments[$count - 1];
	}

	return $vars;
}
