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
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'wishlist.config.php' );
		$wconfig = new WishlistConfig( 'com_wishlist' );
		$this->config = $wconfig;
	}
	
	//-----------
	
	public function &onGroupAreas( $authorized ) 
	{
		$areas = array(
			'wishlist' => JText::_('PLG_GROUPS_WISHLIST')
		);
		return $areas;
	}

	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $areas=null)
	{
		$return = 'html';
		$active = 'wishlist';
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onGroupAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onGroupAreas( $authorized ) ) )) {
				$return = '';
				//$active = $areas[0];
			}
		}
		
		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'dashboard'=>''
		);
		
		ximport('Hubzero_View_Helper_Html');
		
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			if ($return == 'html') {
				ximport('xmodule');
				$arr['html']  = Hubzero_View_Helper_Html::warning( JText::_('GROUPS_LOGIN_NOTICE') );
				$arr['html'] .= XModuleHelper::renderModules('force_mod');
			}
			return $arr;
		}
		
		// Return no data if the user is not authorized
		if (!$authorized || ($authorized != 'admin' && $authorized != 'manager' && $authorized != 'member')) {
			if ($return == 'html') {
				$arr['html'] = Hubzero_View_Helper_Html::warning( JText::_('PLG_GROUPS_WISHLIST_NOTAUTH') );
			}
			return $arr;
		}
		
		if ($return == 'html') {
			JPlugin::loadLanguage( 'com_wishlist' );
			
			$database =& JFactory::getDBO();
			$juser 	  =& JFactory::getUser();

			$option = 'com_wishlist';
			$category = 'group';

			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'wishlist.wishlist.php' );
			//include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'wishlist.config.php' );
			require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'wishlist.html.php' );
			require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'controller.php' );

			include_once( JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php' );

			WishlistController::setVar('_option', $option);
			WishlistController::authorize_admin();

			WishlistController::setVar('category', $category);
			WishlistController::setVar('refid', $group->get('gidNumber'));
			WishlistController::setVar('_task', 'wishlist');
			WishlistController::setVar('_error', 0);
			WishlistController::setVar('config', $this->config);
			WishlistController::setVar('plugin', 1);
			WishlistController::setVar('limit', $this->_params->get('limit'));

			//$banking = (isset($this->config->parameters['banking']) && $this->config->parameters['banking']==1 ) ? 1: 0;
			WishlistController::setVar('banking', 0);  // do not use banking for personal wishlists	

			// Build the final HTML

			//$items = WishlistController::getVar('wishcount');
			//$id = WishlistController::getVar('listid');
			
			$arr['html'] = WishlistController::wishlist();
		}

		// Build the HTML meant for the "overview" tab's metadata overview
		if ($return == 'metadata') {
			//$arr['metadata']  = '<p class="wishlist"><a href="'.JRoute::_('index.php?option=com_groups&gid='.$group->get('cn').'&active=wishlist').'">'.JText::sprintf('PLG_GROUPS_WISHLIST_NUMBER_WISHES',$items).'</a></p>'."\n";
		}

		return $arr;
	}
}
