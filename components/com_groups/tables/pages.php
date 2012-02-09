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
 * Short description for 'GroupPages'
 * 
 * Long description (if any) ...
 */
Class GroupPages extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id = NULL;

	/**
	 * Description for 'gid'
	 * 
	 * @var unknown
	 */
	var $gid = NULL;

	/**
	 * Description for 'url'
	 * 
	 * @var unknown
	 */
	var $url = NULL;

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title = NULL;

	/**
	 * Description for 'content'
	 * 
	 * @var unknown
	 */
	var $content = NULL;

	/**
	 * Description for 'porder'
	 * 
	 * @var unknown
	 */
	var $porder = NULL;

	/**
	 * Description for 'active'
	 * 
	 * @var unknown
	 */
	var $active = NULL;

	/**
	 * Description for 'privacy'
	 * 
	 * @var unknown
	 */
	var $privacy = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	function __construct( &$db)
	{
		parent::__construct( '#__xgroups_pages', 'id', $db );
	}

	////////////////////////////////////////
	// Displaying Pages
	///////////////////////////////////////

	/**
	 * Short description for 'getPages'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @param      boolean $active Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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
					'active' => $page['active'],
					'privacy' => $page['privacy']
					);
			}
		} else {
			$final_pages = array();
		}

		return $final_pages;
	}

	/**
	 * Short description for 'getHighestPageOrder'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getHighestPageOrder( $gid )
	{
		$sql = "SELECT porder from $this->_tbl WHERE gid='".$gid."' ORDER BY porder DESC LIMIT 1";
		$this->_db->setQuery($sql);
		$high = $this->_db->loadAssoc();

		return $high['porder'];
	}

	/**
	 * Short description for 'displayPage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function displayPage()
	{
		$this->juser =& JFactory::getUser();

		//var to hold page content
		$page = '';

		//var to hold if user has access
		$access = true;

		//get overview page access
		$overview_access = $this->group->getPluginAccess('overview');

		//get group discoverability
		$discoverability = $this->group->get('privacy');

		//get the group members
		$members = $this->group->get('members');

		//if user isnt logged in and access level is set to registered users or members only
		if($this->juser->get('guest') && ($overview_access == 'registered' || $overview_access == 'members')) {
			$access = false;
		}

		//if the user isnt a group member or joomla admin
		if(!in_array($this->juser->get('id'),$members) && $overview_access == 'members' && $this->authorized != 'admin') {
			$access = false;
		}

		//if we have failed access and we are on the overview tab or one on the group pages
		if(!$access && ($this->tab == 'overview' || array_key_exists($this->tab,$this->pages))) {
			if($discoverability == 1) {
				JError::raiseError( 404, JText::_('Group Access Denied') );
				return;
			} else {
				return $page = "<p class=\"info\">You do not have the permissions to access this page.</p>";
			}
		}

		//var to hold content and page id
		$pContent = "";
		$pID = "";

		//if overview type is set to custom, use custom content
		if($this->group->get('overview_type') == 1) {
			$pContent = $this->group->get('overview_content');
			$pID = 0;
		} else {
			$page = $this->defaultPage();
			$pID = 0;
		}

		//if we have a page use that content
		if(array_key_exists( $this->tab, $this->pages )) {
			$privacy = $this->pages[$this->tab]['privacy'];
			$privacy = ($privacy != '') ? $privacy : $overview_access;

			if(($privacy == 'registered' && $this->juser->get('guest')) || ($privacy == 'members' && !in_array($this->juser->get('id'), $members))) {
				$pContent = "";
				$page = "<p class=\"info\">You currently dont have the permissions to access this group page.</p>";
			} else {
				$pContent = $this->pages[$this->tab]['content'];
			}

			$pID = $this->pages[$this->tab]['id'];
		}

		//if we have some content
		if($pContent != "") {
			$page = $this->parser->parse( $pContent, $this->config);
		}

		//mark page hit
		if($this->tab == 'overview' || array_key_exists($this->tab, $this->pages)) {
			$this->pageHit($pID);
		}

		//return the page content
		return $page;
	}

	/**
	 * Short description for 'defaultPage'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function defaultPage()
	{
		//get the group members
		$members = $this->group->get('members');
		shuffle($members);

		//get the public and private desc from group object
		$public_desc = $this->group->get('public_desc');
		$private_desc = $this->group->get('private_desc');

		//if there is no public desc use group name
		if ($public_desc == '') {
			$public_desc = $this->group->get('description');
		}

		//if there is no private desc use the public desc
		if($private_desc == '') {
			$private_desc = $public_desc;
		}

		//parse the content with the wiki parser
		$public_desc = $this->parser->parse( stripslashes($public_desc), $this->config );
		$private_desc = $this->parser->parse( stripslashes($private_desc), $this->config );

		//load the member profile lib
		ximport('Hubzero_User_Profile');

		//check if member or manager or Joomla admin
		$isMember = ($this->authorized != 'admin' && $this->authorized != 'manager' && $this->authorized != 'member') ? false : true;

		//$about  = '<h3 class="default">'.JText::_('GROUPS_ABOUT_HEADING').'</h3>'; 
		$about  = '<div class="group-content-header">';
			$about .= '<h3>'.JText::_('GROUPS_ABOUT_HEADING').'</h3>';

		if($isMember && $this->group->get('private_desc') != '') {
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
		$access = $this->group->getPluginAccess('members');

		//check to make sure we should be showing the mini member browser
		if($access == 'nobody' || ($access == 'registered' && $this->juser->get('guest')) || ($access == 'members' && !$isMember)) {
			$member_browser = '';
		} else {
			$member_browser  = '<div class="group-content-header">';
				$member_browser .= '<h3>'.JText::_('GROUPS_GROUP_MEMBERS').'</h3>';
				$member_browser .= '<div class="group-content-header-extra">';
					$member_browser .= '<a href="'.JRoute::_('index.php?option=com_groups&gid='.$this->group->get('cn').'&active=members').'">'.JText::_('VIEW_ALL_MEMBERS').' &rarr;</a>';
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

	/**
	 * Short description for 'pageHit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $pid Parameter description (if any) ...
	 * @return     void
	 */
	private function pageHit( $pid )
	{
		//instantiate database
		$db =& JFactory::getDBO();

		//query to insert page hit
		$sql = "INSERT INTO #__xgroups_pages_hits(gid,pid,uid,datetime,ip)
				VALUES('".$this->group->get('gidNumber')."','".$pid."','".$this->juser->get('id')."',NOW(),'".$_SERVER['REMOTE_ADDR']."')";

		//set the query
		$db->setQuery($sql);

		//perform query
		$db->query();
	}

	/**
	 * Short description for 'thumbit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @param      string $picture Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
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

			if(strlen($uid) == 3) {
				$uid = '00'.$uid;
			}

			if(strlen($uid) == 2) {
				$uid = '000'.$uid;
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
