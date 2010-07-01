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
		$rtrn = 'html';
		$active = 'wishlist';
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onGroupAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onGroupAreas( $authorized ) ) )) {
				$rtrn = '';
				//$active = $areas[0];
			}
		}
		
		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'dashboard'=>''
		);
		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			if ($rtrn == 'html') {
				ximport('xmodule');
				$arr['html']  = Hubzero_View_Helper_Html::warning( JText::_('GROUPS_LOGIN_NOTICE') );
				$arr['html'] .= XModuleHelper::renderModules('force_mod');
			}
			return $arr;
		}
		
		// Return no data if the user is not authorized
		if (!$authorized || ($authorized != 'admin' && $authorized != 'manager' && $authorized != 'member')) {
			if ($rtrn == 'html') {
				$arr['html'] = Hubzero_View_Helper_Html::warning( JText::_('PLG_GROUPS_WISHLIST_NOTAUTH') );
			}
			return $arr;
		}
		
		$option = 'com_wishlist';
		$cat 	= 'group';
		$refid  = $group->get('gidNumber');
		$items  = 0;
		$admin  = 0;
		$html	= '';		
		
		// Include some classes & scripts
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'wishlist.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'wishlist.plan.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'wishlist.owner.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'wishlist.owner.group.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'wish.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'wish.rank.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'wish.attachment.php' );
		ximport('Hubzero_View_Helper_Html');
		require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'controller.php' );
		
		// Configure controller
		WishlistController::setVar('_option', $option);
		WishlistController::setVar('banking', $this->config->get('banking'));
		
		// Get filters
		$filters = WishlistController::getFilters(0);
		$filters['limit'] = $this->_params->get('limit');
		
		// Load some objects
		$obj = new Wishlist( $database );
		$objWish = new Wish( $database );
		$objOwner = new WishlistOwner( $database );
		
		// Get wishlist id
		$id = $obj->get_wishlistID($refid, $cat);
		
		// Create a new list if necessary
		if (!$id) {
			// create private list for group
			ximport('Hubzero_Group');
			if (Hubzero_Group::exists($refid)) {
				$group = new XGroup();
				$group->select($refid);	
				$id = $obj->createlist($cat, $refid, 0, $group->get('cn').' '.JText::_('WISHLIST_NAME_GROUP'));
			}			
		}
		
		// get wishlist data
		$wishlist = $obj->get_wishlist($id, $refid, $cat);
		
		if(!$wishlist) {
			$html = Hubzero_View_Helper_Html::error(JText::_('ERROR_WISHLIST_NOT_FOUND'));
		}
		else {
			// Get list owners
			$owners   = $objOwner->get_owners($id, $this->config->get('group') , $wishlist);
			
			// Authorize admins & list owners
			if(!$juser->get('guest')) {
				if ($juser->authorize($option, 'manage')) {
					$admin = 1;
				}
				if(in_array($juser->get('id'), $owners['individuals'])) {
					$admin = 2;
				}
				else if(in_array($juser->get('id'), $owners['advisory'])) {
					$admin = 3;
				}
			}
			else if(!$wishlist->public && $rtrn != 'metadata') {
				// not authorized
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			
			$items = $objWish->get_count ($id, $filters, $admin);	
			
			if($rtrn == 'html') {
				// Add the CSS to the template
				WishlistController::_getStyles();
				
				// Thumbs voting CSS & JS
				WishlistController::_getStyles('com_answers', 'vote.css');
				
				// Get wishes
				$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $admin, $juser);
				
				$title = ($admin) ?  JText::_('WISHLIST_TITLE_PRIORITIZED') : JText::_('WISHLIST_TITLE_RECENT_WISHES');
				if(count($wishlist->items) > 0 && $items > $filters['limit']) {
					$title.= ' (<a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid).'">'.JText::_('view all') .' '.$items.'</a>)';
				}
				else {
					$title .= ' ('.$items.')';
				}
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
	
				// Pass the view some info
				$view->option = $option;
				$view->title = $title;
				$view->wishlist = $wishlist;
				$view->filters = $filters;
				$view->admin = $admin;
				$view->config = $this->config;
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}
	
				// Return the output
				$arr['html'] = $view->loadTemplate();
				}						
		}
					
		// Build the HTML meant for the "about" tab's metadata overview
		$metadata = '';
		if ($rtrn == 'metadata') {
				//$metadata  = '<p class="wishlist"><a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id.a.'active=wishlist').'">'.JText::sprintf('NUM_WISHES',$items);
				//$metadata .= '</a> (<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$id.a.'task=add').'">'.JText::_('ADD_NEW_WISH').'</a>)</p>'.n;
		}

		return $arr;
	}
	
	//-------------------
}