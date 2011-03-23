<?php

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