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
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for group page
 */
Class GroupPages extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $gid = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $url = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $title = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $content = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $porder = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $active = NULL;

	/**
	 * varchar(10)
	 * 
	 * @var string
	 */
	var $privacy = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_pages', 'id', $db);
	}

	/**
	 * Get pages for a group
	 * 
	 * @param      string  $gid    Group alias (cn)
	 * @param      boolean $active Parameter description (if any) ...
	 * @return     array
	 */
	public function getPages($gid, $active = false)
	{
		$final = array();

		if ($active) 
		{
			$sql = "SELECT * FROM $this->_tbl WHERE gid='$gid' AND active=1 ORDER BY porder ASC";
		}
		else 
		{
			$sql = "SELECT * FROM $this->_tbl WHERE gid='$gid' ORDER BY porder ASC";
		}

		$this->_db->setQuery($sql);
		$pages = $this->_db->loadAssocList();

		if (count($pages) > 0) 
		{
			foreach ($pages as $page) 
			{
				$final[$page['url']] = array(
					'id'      => $page['id'],
					'url'     => $page['url'],
					'title'   => $page['title'],
					'content' => $page['content'],
					'order'   => $page['porder'],
					'active'  => $page['active'],
					'privacy' => $page['privacy']
				);
			}
		}

		return $final;
	}

	/**
	 * Get the last page in the ordering
	 * 
	 * @param      string  $gid    Group alias (cn)
	 * @return     integer
	 */
	public function getHighestPageOrder($gid)
	{
		$sql = "SELECT porder from $this->_tbl WHERE gid='$gid' ORDER BY porder DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build a page for display
	 * 
	 * @return     string HTML
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
		if ($this->juser->get('guest') && ($overview_access == 'registered' || $overview_access == 'members')) 
		{
			$access = false;
		}

		//if the user isnt a group member or joomla admin
		if (!in_array($this->juser->get('id'),$members) && $overview_access == 'members') 
		{
			$access = false;
		}

		//if we have failed access and we are on the overview tab or one on the group pages
		if (!$access && ($this->tab == 'overview' || array_key_exists($this->tab,$this->pages))) 
		{
			if ($discoverability == 1) 
			{
				JError::raiseError(404, JText::_('Group Access Denied'));
				return;
			} 
			else 
			{
				return $page = '<p class="info">You do not have the permissions to access this page.</p>';
			}
		}

		//var to hold content and page id
		$pContent = '';
		$pID = '';

		//if overview type is set to custom, use custom content
		if ($this->group->get('overview_type') == 1) 
		{
			$pContent = $this->group->get('overview_content');
			$pID = 0;
		} 
		else 
		{
			$page = $this->defaultPage();
			$pID = 0;
		}

		//if we have a page use that content
		if (array_key_exists($this->tab, $this->pages)) 
		{
			$privacy = $this->pages[$this->tab]['privacy'];
			$privacy = ($privacy != '') ? $privacy : $overview_access;

			if (($privacy == 'registered' && $this->juser->get('guest')) || ($privacy == 'members' && !in_array($this->juser->get('id'), $members))) 
			{
				$pContent = '';
				$page = '<p class="info">You currently dont have the permissions to access this group page.</p>';
			} 
			else 
			{
				$pContent = $this->pages[$this->tab]['content'];
			}

			$pID = $this->pages[$this->tab]['id'];
		}

		//if we have some content
		if ($pContent != '')
		{
			$page = $this->parser->parse($pContent, $this->config);
		}

		//mark page hit
		if ($this->tab == 'overview' || array_key_exists($this->tab, $this->pages)) 
		{
			$this->pageHit($pID);
		}

		//return the page content
		return $page;
	}

	/**
	 * Display the default group page
	 * 
	 * @return     string HTML
	 */
	public function defaultPage()
	{
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		//get the group members
		$members = $this->group->get('members');
		shuffle($members);

		$oparams = JComponentHelper::getParams("com_groups"); 
		$o_system_users = $oparams->get('display_system_users', 'no');

		$gparams = new $paramsClass($this->group->get('params'));
		$g_system_users = $gparams->get('display_system_users', "global");

		switch($g_system_users)
		{
			case 'yes':
				$display_system_users = 'yes';
				break;
			case 'no':
				$display_system_users = 'no';
				break;
			case 'global':
				$display_system_users = $o_system_users;
				break;
		}

		//callback to check if user is system user
		function isSystemUser2($user) 
		{
			return ($user < 1000) ? false : true;
		}

		//if we dont want to display system users
		//filter values through callback above and then reset array keys
		if ($display_system_users == 'no') 
		{
			$members = array_values(array_filter($members, 'isSystemUser2'));
		}

		//get the public and private desc from group object
		$public_desc  = $this->group->get('public_desc');
		$private_desc = $this->group->get('private_desc');

		//if there is no public desc use group name
		if ($public_desc == '') 
		{
			$public_desc = $this->group->get('description');
		}

		//parse the content with the wiki parser
		$public_desc = $this->parser->parse(stripslashes($public_desc), $this->config);

		//if there is no private desc use the public desc
		if ($private_desc == '') 
		{
			$private_desc = $public_desc;
		} 
		else 
		{
			$private_desc = $this->parser->parse(stripslashes($private_desc), $this->config);
		}

		//load the member profile lib
		ximport('Hubzero_User_Profile');

		//check if member or manager
		$isMember = ($this->authorized == 'manager' || $this->authorized == 'member') ? true : false;
 
		$about  = '<div class="group-content-header">';
			$about .= '<h3>' . JText::_('GROUPS_ABOUT_HEADING') . '</h3>';

		if ($isMember && $this->group->get('private_desc') != '') 
		{
			$about .= '<div class="group-content-header-extra">';
				$about .= '<a id="toggle_description" class="hide" href="#">Show Public Description (+)</a>';
			$about .= '</div><!-- / .group-content-header-extra -->';
			$about .= '</div><!-- / .group-content-header -->';

			$about .= '<div id="description">';
				$about .= '<span id="private">' . $private_desc . '</span>';
				$about .= '<span id="public" class="hide">' . $public_desc . '</span>';
			$about .= '</div><!-- / #description -->';
		} 
		else 
		{
			$about .= '</div><!-- / .group-content-header -->';
			$about .= '<div id="description">' . $public_desc . '</div><!-- / #description -->';
		}

		$about .= '<br />';

		//get the members plugin access for this group
		$access = $this->group->getPluginAccess('members');

		//check to make sure we should be showing the mini member browser
		if ($access == 'nobody' || ($access == 'registered' && $this->juser->get('guest')) || ($access == 'members' && !$isMember)) 
		{
			$member_browser = '';
		} 
		else 
		{
			$member_browser  = '<div class="group-content-header">';
				$member_browser .= '<h3>' . JText::_('GROUPS_GROUP_MEMBERS') . '</h3>';
				$member_browser .= '<div class="group-content-header-extra">';
					$member_browser .= '<a href="' . JRoute::_('index.php?option=com_groups&gid=' . $this->group->get('cn') . '&active=members') . '">' . JText::_('VIEW_ALL_MEMBERS') . ' &rarr;</a>';
				$member_browser .= '</div>';
			$member_browser .= '</div>';

			$counter = 0;
			$member_browser .= '<div id="member_browser">';

			foreach ($members as $member) 
			{
				$counter++;
				if ($counter <= 12) 
				{
					$user =& Hubzero_User_Profile::getInstance($member);
					if (is_object($user))
					{
						$cls = "member-border";
						$picture  = $this->thumbit($user);
						$link = JRoute::_('index.php?option=com_members&id='.$user->get("uidNumber"));
						
						$member_browser .= '<a href="' . $link . '" class="member" title="Go to ' . stripslashes($user->get('name')) . '\'s Profile.">';
						$member_browser .= '<img src="' . $picture . '" alt="' . $user->get('name') . '" class="' . $cls . '" width="50px" height="50px" />';
						$member_browser .= '<span class="name">' . stripslashes($user->get('name')) . '</span>';
						$member_browser .= '<span class="org">' . stripslashes($user->get('organization')) . '</span>';
						$member_browser .= '</a>';
					}
				}
			}

			$member_browser .= '</div>';
		}

	 	return $about.$member_browser;
	}

	/**
	 * Record a page hit
	 * 
	 * @param      integer $pid Page ID
	 * @return     void
	 */
	private function pageHit($pid)
	{
		//query to insert page hit
		$sql = "INSERT INTO #__xgroups_pages_hits(gid,pid,uid,datetime,ip)
				VALUES('" . $this->group->get('gidNumber') . "','" . $pid . "','" . $this->juser->get('id') . "',NOW(),'" . $_SERVER['REMOTE_ADDR'] . "')";

		//set the query
		$this->_db->setQuery($sql);

		//perform query
		$this->_db->query();
	}

	/**
	 * Create a thumbnail for a user
	 * 
	 * @param      object $user Hubzero_User_Profile
	 * @return     string
	 */
	private function thumbit($user)
	{
		//load members config
		$config = JComponentHelper::getParams('com_members');

		//get default picture
		$default_picture = $config->get('defaultpic');

		//get user picture
		$picture = $user->get('picture');

		//if user has no picture set return default
		if ($picture == '')
		{
			return $default_picture;
		}

		//build path to users profile
		$path = ltrim(rtrim($config->get('webpath'), DS), DS);
		$path .= DS . Hubzero_View_Helper_Html::niceidformat($user->get('uidNumber')) . DS;

		//build path to old uploaded thumbs
		$parts = explode('.', $user->get('picture'));
		$ext = array_pop($parts);
		$name = implode('.', $parts) . '_thumb';

		//paths to thumbs thumb1 is preferred
		$thumb1 = $path . 'thumb.png';
		$thumb2 = $path . $name . '.' . $ext;

		//return profile thumb based on existence
		if (file_exists(JPATH_ROOT . DS . $thumb1))
		{
			return $thumb1;
		}
		elseif (file_exists(JPATH_ROOT . DS . $thumb2))
		{
			return $thumb2;
		}
		else
		{
			$full = JPATH_ROOT . DS . $path . $picture;
			if (file_exists($full))
			{
				echo $user->get('uidNumber') .'<br />';
				echo $full .'<br />';
				ximport('Hubzero_Image');
				$hi = new Hubzero_Image($full);
				$hi->resize(50, false, true, true);
				$hi->save($thumb1);
				return $thumb1;
			}
			else
			{
				return $default_picture;
			}
		}
	}
}
