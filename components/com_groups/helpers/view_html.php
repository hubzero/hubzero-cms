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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'JHTMLView_html'
 * 
 * Long description (if any) ...
 */
Class JHTMLView_html {

	/**
	 * Short description for 'displayMenu'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $user Parameter description (if any) ...
	 * @param      string $authorized Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @param      mixed $group Parameter description (if any) ...
	 * @param      array $group_content_pages Parameter description (if any) ...
	 * @param      unknown $active_tab Parameter description (if any) ...
	 * @param      string $access Parameter description (if any) ...
	 * @param      string $name Parameter description (if any) ...
	 * @param      unknown $title Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

		if($name == 'overview' && count($group_content_pages) > 0) {

			$cls = ($real_tab != $active_tab) ? '' : $cls;

			if(($access == 'registered' && $user->get('guest')) || ($access == 'members' && !in_array($user->get('id'), $group_members))) {
				$links  = "<li class=\"protected\">{$title}";
				$links .= "<ul class=\"overview_protected\">";
			} else {
				$links  = "<li>";
				$links .= "<a class=\"{$cls}\" href=\"{$link}\">{$title}</a>";
				$links .= "<ul>";
			}

			foreach($group_content_pages as $page) {

				$default_sub_access = $access;
				$sub_access = ($page['privacy'] == 'members') ? "members" : $default_sub_access;

				$sub_cls = ($page['url'] == $real_tab) ? 'active': '';
				$sub_link = JRoute::_('index.php?option='.$option.'&gid='.$group->get('cn').'&active='.$page['url']);

				if(($sub_access == "registered" && $user->get('guest')) || ($sub_access == "members" && !in_array($user->get('id'),$group_members))) {
					$links .= "<li class=\"sub_protected\">{$page['title']}<span></span></li>";
				} else {
					$links .= "<li><a class=\"{$sub_cls}\" href=\"{$sub_link}\">{$page['title']}</a></li>";
				}
			}

			$links .= "</ul>";
			$links .= "</li>";
			return $links;
		}

		if($access == 'registered' && $user->get('guest') && !$admin) {
			return "<li class=\"protected\">{$title}</li>";
		}

		if($access == 'members' && !in_array($user->get('id'), $group_members) && !$admin) {
			return "<li class=\"protected\">{$title}</li>";
		}

		return "<li><a class=\"{$cls}\" href=\"{$link}\">{$title}</a></li>";
	}

	/**
	 * Short description for 'displayContent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $user Parameter description (if any) ...
	 * @param      unknown $group Parameter description (if any) ...
	 * @param      unknown $active_tab Parameter description (if any) ...
	 * @param      array $sections Parameter description (if any) ...
	 * @param      array $hub_plugins Parameter description (if any) ...
	 * @param      unknown $group_plugins Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	function displayContent($user, $group, $active_tab, $sections, $hub_plugins, $group_plugins)
	{
		for($i=0;$i < count($hub_plugins); $i++) {
			if($active_tab == $hub_plugins[$i]['name']) {
				return $sections[$i]['html'];
				break;
			}
		}
	}

}

?>