<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_wishlist' );
JPlugin::loadLanguage( 'com_wishlist' );
	
//-----------

class plgGroupsWishlist extends JPlugin
{
	public function plgGroupsWishlist(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'wishlist' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		// Get the component parameters
		$wconfig = & JComponentHelper::getParams( 'com_wishlist' );
		$this->config = $wconfig;
	}
	
	//-----------
	
	public function &onGroupAreas() 
	{
		$area = array(
			'name' => 'wishlist',
			'title' => JText::_('PLG_GROUPS_WISHLIST'),
			'default_access' => 'registered'
		);
		
		return $area;
	}
	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
	{
		$return = 'html';
		$active = 'wishlist';
		
		// The output array we're returning
		$arr = array(
			'html'=>''
		);
		
		//get this area details
		$this_area = $this->onGroupAreas();
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if(!in_array($this_area['name'],$areas)) {
				return;
			}
		}
		
		//if we want to return content
		if ($return == 'html') {
			//set group members plugin access level
			$group_plugin_acl = $access[$active];
		
			//Create user object
			$juser =& JFactory::getUser();
		
			//get the group members
			$members = $group->get('members');
	
			//if set to nobody make sure cant access
			if($group_plugin_acl == 'nobody') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_OFF', ucfirst($active))."</p>";
				return $arr;
			}
			
			//check if guest and force login if plugin access is registered or members
			if ($juser->get('guest') && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')) {
				ximport('Hubzero_Module_Helper');
				$arr['html']  = "<p class=\"warning\">".JText::sprintf('GROUPS_PLUGIN_REGISTERED', ucfirst($active))."</p>";
				$arr['html'] .= Hubzero_Module_Helper::renderModules('force_mod');
				return $arr;
			}
			
			//check to see if user is member and plugin access requires members
			if(!in_array($juser->get('id'),$members) && $group_plugin_acl == 'members' && $authorized != 'admin') {
				$arr['html'] = "<p class=\"info\">".JText::sprintf('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active))."</p>";
				return $arr;
			}
			
			//instantiate database
			$database =& JFactory::getDBO();
			
			// Set some variables so other functions have access
			$this->juser = $juser;
			$this->database = $database;
			$this->authorized = $authorized;
			$this->members = $members;
			$this->group = $group;
			$this->option = $option;
			$this->action = $action;
			
			//include com_wishlist files
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.plan.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wishlist.owner.group.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.rank.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'tables'.DS.'wish.attachment.php' );
			require_once( JPATH_ROOT.DS.'components'.DS.'com_wishlist'.DS.'controller.php' );
			
			//import hubzero libararys
			ximport('Hubzero_View_Helper_Html');
			ximport('Hubzero_Group');
			ximport('Hubzero_Document');
			
			//set some more vars
			$gid = $this->group->get('gidNumber');
			$cn = $this->group->get('cn');
			$category = 'group';
			$admin = 0;
			
			// Configure controller
			WishlistController::setVar('_option', 'com_wishlist');
			WishlistController::setVar('banking', $this->config->get('banking'));
			
			// Get filters
			$filters = WishlistController::getFilters(0);
			$filters['limit'] = $this->_params->get('limit');

			// Load some objects
			$obj = new Wishlist( $this->database );
			$objWish = new Wish( $this->database );
			$objOwner = new WishlistOwner( $this->database );

			// Get wishlist id
			$id = $obj->get_wishlistID($gid, $category);
			
			// Create a new list if necessary
			if (!$id) {
				// create private list for group
				if (Hubzero_Group::exists($gid)) {
					$group = Hubzero_Group::getInstance($gid);
					$id = $obj->createlist($category, $gid, 0, $cn.' '.JText::_('WISHLIST_NAME_GROUP'));
				}			
			}
			
			// get wishlist data
			$wishlist = $obj->get_wishlist($id, $gid, $category);
			
			//if we dont have a wishlist display error
			if(!$wishlist) {
				$arr['html'] = Hubzero_View_Helper_Html::error(JText::_('ERROR_WISHLIST_NOT_FOUND'));
				return $arr;
			}
			
			// Get list owners
			$owners = $objOwner->get_owners($id, $this->config->get('group'), $wishlist);
			
			//if user is guest and wishlist isnt public
			//if(!$wishlist->public && $juser->get('guest')) {
			//	$arr['html'] = Hubzero_View_Helper_Html::warning(JText::_('The Group Wishlist is not a publicly viewable list.'));
			//	return $arr;
			//}
			
			// Authorize admins & list owners
			if ($juser->authorize($option, 'manage')) {
				$admin = 1;
			}
			
			//authorized based on wishlist
			if(in_array($juser->get('id'), $owners['individuals'])) {
				$admin = 2;
			} else if(in_array($juser->get('id'), $owners['advisory'])) {
				$admin = 3;
			}
			
			//get item count
			$items = $objWish->get_count($id, $filters, $admin);
			
			// Get wishes
			$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $admin, $juser);
			
			// HTML output
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'groups',
					'element'=>'wishlist',
					'name'=>'browse'
				)
			);
		
			//push the stylesheet to the view
			Hubzero_Document::addPluginStylesheet('groups', 'wishlist');

			// Pass the view some info
			$view->option = $option;
			//$view->owners = $owners;
			$view->group = $this->group;
			$view->juser = $juser;
			$view->wishlist = $wishlist;
			$view->items = $items;
			$view->filters = $filters;
			$view->admin = $admin;
			$view->config = $this->config;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
			
		}
		return $arr;
	}
	
	//-------------------
}
