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

Class GroupPages extends JTable
{
	var $id = NULL;
	var $gid = NULL;
	var $url = NULL;
	var $title = NULL;
	var $content = NULL;
	var $porder = NULL;
	var $active = NULL;
	
	function __construct( &$db)
	{
		parent::__construct( '#__xgroups_pages', 'id', $db );
	}
	
	////////////////////////////////////////
	// Displaying Pages
	///////////////////////////////////////
	
	public function getPages( $gid, $active = false ) 
	{
		$final_pages = array();
		
		if($active) {
			$sql = "SELECT * FROM $this->_tbl WHERE gid='".$gid."' AND active=1 ORDER BY porder ASC";
		} else {
			$sql = "SELECT * FROM $this->_tbl WHERE gid='".$gid."' ORDER BY porder ASC";
		}
		
		$this->_db->setQuery($sql);
		$pages = $this->_db->loadAssocList();
		
		if(count($pages) > 0) {
			foreach($pages as $page) {
				$final_pages[$page['url']] = array(
					'id' => $page['id'],
					'url' => $page['url'],
					'title' => $page['title'],
					'content' => $page['content'],
					'order' => $page['porder'],
					'active' => $page['active'] 
					);
			}
		} else {
			$final_pages = array();
		}
		
		return $final_pages;
	}
	
	//-------
	
	public function getHighestPageOrder( $gid )
	{
		$sql = "SELECT porder from $this->_tbl WHERE gid='".$gid."' ORDER BY porder DESC LIMIT 1";
		$this->_db->setQuery($sql);
		$high = $this->_db->loadAssoc();
		
		return $high['porder'];
	}
	
	//------
	
	public function displayOverviewPage( $group, $authorized, $tab, $pages, $parser, $wikiconfig, $option, $user )
	{
		$page = '';
		
		//$this->print_pretty($pages);
		if(array_key_exists($tab,$pages)) {
			$page = $parser->parse("\n".stripslashes($pages[$tab]['content']), $wikiconfig);
		} else {
			$overview_type = $group->get('overview_type');
			switch($overview_type)
			{
				case 1:		$page = $parser->parse("\n".stripslashes($group->get('overview_content')), $wikiconfig);		break;
				case 0:
				default:	$page = $this->defaultPage($authorized, $group, $parser, $wikiconfig, $option, $user );			break;
			}
		}
		
		return $page;
	}
	
	//-----------
	
	public function defaultPage(  $authorized, $group, $parser, $wikiconfig, $option, $user ) 
	{
		//get the group members
		$members = $group->get('members');
		shuffle($members); 
		
		//get the public and private desc from group object
		$public_desc = $group->get('public_desc');
		$private_desc = $group->get('private_desc');
		
		//Get overview page content
		$overview_page = $group->get('overview_content');
		
		//if there is no public desc use group name
		if ($public_desc == '') {
			$public_desc = $group->get('description');
		}
		
		//if there is no private desc use the public desc
		if($private_desc == '') {
			$private_desc = $public_desc;
		}
		
		//parse the content with the wiki parser
		$public_desc = $parser->parse( "\n".stripslashes($public_desc), $wikiconfig );
		$private_desc = $parser->parse( "\n".stripslashes($private_desc), $wikiconfig );
		$overview_page = $parser->parse( "\n".stripslashes($overview_page), $wikiconfig );
		
		//load the member profile lib
		ximport('Hubzero_User_Profile');
		
		//check if member or manager or Joomla admin
		$isMember = ($authorized != 'admin' && $authorized != 'manager' && $authorized != 'member') ? false : true;
		
		//$about  = '<h3 class="default">'.JText::_('GROUPS_ABOUT_HEADING').'</h3>'; 
		$about  = '<div class="group-content-header">';
			$about .= '<h3>'.JText::_('GROUPS_ABOUT_HEADING').'</h3>';
		
		if($isMember && $group->get('private_desc') != '') {
			$about .= '<div class="group-content-header-extra">';
				$about .= '<a id="toggle_description" class="hide" href="#">Show Public Description (+)</a>';
			$about .= '</div>';
			$about .= '</div>';
			
			$about .= '<div id="description">';
				$about .= '<span id="private">'.$private_desc.'</span>';
				$about .= '<span id="public" class="hide">'.$public_desc.'</span>';
			$about .= '</div>';
		} else {
			$about .= '</div>';
			$about .= $public_desc;
		}
		
		$about .= '<br />';
		
		//get the members plugin access for this group
		$access = $group->getPluginAccess($group, 'members');
		
		//check to make sure we should be showing the mini member browser
		if($access == 'nobody' || ($access == 'registered' && $user->get('guest')) || ($access == 'members' && !$isMember)) {
			$member_browser = '';
		} else {
			$member_browser  = '<div class="group-content-header">';
				$member_browser .= '<h3>'.JText::_('GROUPS_GROUP_MEMBERS').'</h3>';
				$member_browser .= '<div class="group-content-header-extra">';
					$member_browser .= '<a href="'.JRoute::_('index.php?option='.$option.'&gid='.$group->get('cn').'&active=members').'">'.JText::_('VIEW_ALL_MEMBERS').' &rarr;</a>';
				$member_browser .= '</div>';
			$member_browser .= '</div>';
		
			$counter = 0;
			$member_browser .= '<div id="member_browser">';
		
			foreach($members as $member) { 
				$counter++;
				if($counter < 8) {
					$u = new Hubzero_User_Profile();
					$u->load( $member );
					$member_browser .= '<a class="member" href="'.JRoute::_('index.php?option=com_members&id='.$u->get('uidNumber')).'">';
						$picture = $this->thumbit($u->get('uidNumber'), $u->get('picture'));
						$cls = (!$this->default_thumb) ? "member-border" : "";
						$member_browser .= '<img class="'.$cls.'" src="'.$picture.'" alt="'.$u->get('name').'" width="50px" height="50px" /><br />';
						$member_browser .= '<span class="name">'.$u->get('name').'</span>';
					$member_browser .= '</a>';
				}
			}
		
			$member_browser .= '</div>';
		}
		
	 	return $about.$member_browser;
	}
	
	//------------
	
	private function thumbit( $uid, $picture )
	{
		//set a default
		$this->default_thumb = true;
		
		//set the default for the member picture
		$default_member_thumb = 'components'.DS.'com_groups'.DS.'assets'.DS.'img'.DS.'default_member_picture.jpg';
		
		//check if picture is empty
		if($picture != '') {
			//split the picture into parts
			$pic_parts = explode(".", $picture);
			
			if(strlen($uid) == 4) {
				$uid = '0'.$uid;
			}
			
			//build the thumb path
			$thumb = $pic_parts[0]."_thumb.".$pic_parts[1];
			$path = 'site'.DS.'members'.DS.$uid.DS;
			
			//check if picture exits
			if (is_file($path.$thumb)) {
				$return = $path.$thumb;
				$this->default_thumb = false;
			} elseif(is_file($path.$picture)) {
				$return = $path.$picture;
				$this->default_thumb = false;
			} else {
				$return = $default_member_thumb;
				$this->default_thumb = true;
			}		
		} else {
			$return = $default_member_thumb;
			$this->default_thumb = true;
		}
		
		//return picture
		return $return;
	} 
	
	//-----
}
?>