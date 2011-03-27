<?php
/**
 * @package     hubzero-cms
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

Class JHTMLView_html {
	
	function displayMenu($user, $authorized, $option, $group, $group_content_pages, $active_tab, $access, $name, $title)
	{
		//check if admin
		$admin = false;
		
		if($authorized == 'admin') {
			$admin = true;
		}
		
		$group_members = $group->get('members');
		$real_tab = JRequest::getVar('active','overview');
		
		$page_urls = array();
		foreach($group_content_pages as $page) {
			array_push($page_urls, $page['url']);
		}
		
		$cls = ($active_tab == $name) ? 'active' : '' ;
		$link = JRoute::_('index.php?option='.$option.'&gid='.$group->get('cn').'&active='.$name);
	
		if($access == 'nobody') {
			return '';
		}
		
		if($access == 'registered' && $user->get('guest') && !$admin) {
			return "<li class=\"protected\">{$title}</li>";
		}
		
		if($access == 'members' && !in_array($user->get('id'), $group_members) && !$admin) {
			return "<li class=\"protected\">{$title}</li>";
		}
		
		if($name == 'overview' && count($group_content_pages) > 0) {
			
			$cls = ($real_tab != $active_tab) ? '' : $cls;
			
			$links  = "<li>";
			$links .= "<a class=\"{$cls}\" href=\"{$link}\">{$title}</a>";
				$links .= "<ul>";
					foreach($group_content_pages as $page) {
						
						$sub_cls = ($page['url'] == $real_tab) ? 'active': '';
						$sub_link = JRoute::_('index.php?option='.$option.'&gid='.$group->get('cn').'&active='.$page['url']);
						
						$links .= "<li><a class=\"{$sub_cls}\" href=\"{$sub_link}\">{$page['title']}</a></li>";
					}
				$links .= "</ul>";
			$links .= "</li>";
			return $links;
		}
		
		return "<li><a class=\"{$cls}\" href=\"{$link}\">{$title}</a></li>";
	}
	
	//--------
	
	function displayContent($user, $group, $active_tab, $sections, $hub_plugins, $group_plugins)
	{
		/*
		$group_members = $group->get('members');	
		
		if($group_plugins[$active_tab] == 'nobody') {
			return "<p class=\"info\">".JText::_('GROUPS_PLUGIN_OFF')."</p>";
		}
		
		if($group_plugins[$active_tab] == 'registered' && $user->get('guest')) {
			return "<p class=\"info\">".JText::_('GROUPS_PLUGIN_REGISTERED')."</p>";
		}
		
		if($group_plugins[$active_tab] == 'members' && !in_array($user->get('id'), $group_members)) {
			return "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_MEMBER', JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&task=join'))."</p>";
		}
		
		if(!array_key_exists($active_tab, $group_plugins)) {
			return "<p class=\"info\">".JText::_('GROUPS_PLUGIN_NOT_VALID')."</p>";
		}
		*/
		
		
		for($i=0;$i < count($hub_plugins); $i++) {
			if($active_tab == $hub_plugins[$i]['name']) {
				return $sections[$i]['html'];
				break;
			}
		}
	}
	
	//---------
	
	
}

?>