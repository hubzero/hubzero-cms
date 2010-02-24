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
JPlugin::loadLanguage( 'plg_resources_wishlist' );
JPlugin::loadLanguage( 'com_wishlist' );
	
//-----------

class plgResourcesWishlist extends JPlugin
{
	public function plgResourcesWishlist(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'wishlist' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		// Get the component parameters
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'wishlist.config.php' );
		$wconfig = new WishlistConfig( 'com_wishlist' );
		$this->config = $wconfig;
	}
	
	//-----------
	
	public function &onResourcesAreas( $resource ) 
	{
		if ($resource->type != 7) {
			$areas = array();
		} else {
			$areas = array(
				'wishlist' => JText::_('Wishlist')
			);
		}
		
		return $areas;
	}

	//-----------

	public function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		// Display only for tools
		if ($resource->type != 7) {
			return $arr;
		}

		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		
		$option = 'com_wishlist';
		$category = 'resource';
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'wishlist.wishlist.php' );
		require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'wishlist.html.php' );
		require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'controller.php' );	
		include_once( JPATH_ROOT.DS.'plugins'.DS.'xhub'.DS.'xlibraries'.DS.'xcomment.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
		
		WishlistController::setVar('_option', $option);
		WishlistController::authorize_admin();
		
		WishlistController::setVar('category', $category);
		WishlistController::setVar('refid', $resource->id);
		WishlistController::setVar('_task', 'wishlist');
		WishlistController::setVar('_error', 0);
		WishlistController::setVar('config', $this->config);
		WishlistController::setVar('plugin', 1);
		WishlistController::setVar('limit', $this->_params->get('limit'));
		
		$banking = (isset($this->config->parameters['banking']) && $this->config->parameters['banking']==1 ) ? 1: 0;
		WishlistController::setVar('banking', $banking);
		
		if ($rtrn != 'metadata') {
			$arr['html'] = WishlistController::wishlist();	
			$items = WishlistController::getVar('wishcount');
			$id    = WishlistController::getVar('listid');
		} else {
			$obj = new Wishlist( $database );
			$id = $obj->get_wishlistID($resource->id, $category);
			$objWish = new Wish( $database );
			$filters = WishlistController::getFilters(0);
			$admin 	= WishlistController::getVar('_admin');
			$items = $objWish->get_count($id, $filters, $admin);
		}
		
		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			$arr['metadata']  = '<p class="wishlist"><a href="'.JRoute::_('index.php?option=com_resources&id='.$resource->id.'&active=wishlist').'">'.JText::sprintf('NUM_WISHES',$items).'</a> ';
			$arr['metadata'] .= '(<a href="'.JRoute::_('index.php?option='.$option.'&id='.$id.'&task=add').'">'.JText::_('Add a new wish').'</a>)</p>';
		}

		return $arr;
	}
}
