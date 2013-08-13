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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Plugin');
	
/**
 * Publications Plugin class for wishes
 */
class plgPublicationsWishlist extends JPlugin
{
	
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'publications', 'wishlist' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		// Get the component parameters
		$wconfig = & JComponentHelper::getParams( 'com_wishlist' );
		$this->config = $wconfig;

		$this->loadLanguage();

		$lang = JFactory::getLanguage();
		$lang->load('com_wishlist');
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $publication 	Current publication
	 * @param      string $version 		Version name
	 * @param      boolean $extended 	Whether or not to show panel
	 * @return     array
	 */	
	function &onPublicationAreas( $publication, $version = 'default', $extended = true ) 
	{
		if ($publication->_category->_params->get('plg_wishlist') && $extended) 
		{
			$areas = array(
				'wishlist' => JText::_('Wishlist')
			);
		} 
		else 
		{
			$areas = array();
		}
		
		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  	$publication 	Current publication
	 * @param      string  	$option    		Name of the component
	 * @param      array   	$areas     		Active area(s)
	 * @param      string  	$rtrn      		Data to be returned
	 * @param      string 	$version 		Version name
	 * @param      boolean 	$extended 		Whether or not to show panel	
	 * @return     array
	 */	
	function onPublication( $publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) 
		{
			if (!array_intersect( $areas, $this->onPublicationAreas( $publication ) ) 
			&& !array_intersect( $areas, array_keys( $this->onPublicationAreas( $publication ) ) )) 
			{
				$rtrn = 'metadata';
			}
		}
				
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		$option = 'com_wishlist';
		$cat 	= 'publication';
		$refid  = $publication->id;
		$items  = 0;
		$admin  = 0;
		$html	= '';		
		
		// Include some classes & scripts
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'wishlist.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'wishlist.plan.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'wishlist.owner.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'wishlist.owner.group.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'wish.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'wish.rank.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $option . DS . 'tables' . DS . 'wish.attachment.php' );
		ximport('Hubzero_View_Helper_Html');
		require_once( JPATH_ROOT . DS . 'components' . DS . $option . DS . 'controllers' . DS . 'wishlist.php' );
		
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
		if (!$id) 
		{
			if ($publication->title && $publication->state == 1) 
			{
				$rtitle = isset($publication->alias) && $publication->alias
				? JText::_('WISHLIST_NAME_RESOURCE').' '.$publication->alias 
				: JText::_('WISHLIST_NAME_PUB_ID').' '.$publication->id;
				$id = $obj->createlist($cat, $refid, 1, $rtitle, $publication->title);
			}				
		}
		
		// get wishlist data
		$wishlist = $obj->get_wishlist($id, $refid, $cat);
		
		if (!$wishlist) 
		{
			$html = Hubzero_View_Helper_Html::error(JText::_('ERROR_WISHLIST_NOT_FOUND'));
		} 
		else 
		{
			// Get list owners
			$owners = $objOwner->get_owners($id, $this->config->get('group') , $wishlist);
			
			// Authorize admins & list owners
			if (!$juser->get('guest')) 
			{
				if ($juser->authorize($option, 'manage')) 
				{
					$admin = 1;
				}
				if (in_array($juser->get('id'), $owners['individuals'])) 
				{
					$admin = 2;
				} 
				elseif (in_array($juser->get('id'), $owners['advisory'])) 
				{
					$admin = 3;
				}
			} 
			elseif (!$wishlist->public && $rtrn != 'metadata') 
			{
				// not authorized
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
			
			$items = $objWish->get_count ($id, $filters, $admin);	
			
			if ($rtrn != 'metadata') 
			{
				// Add the CSS to the template
				WishlistController::_getStyles();
				
				// Thumbs voting CSS & JS
				WishlistController::_getStyles('com_answers', 'vote.css');
				
				// Get wishes
				$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $admin, $juser);
				
				$title = ($admin) ?  JText::_('WISHLIST_TITLE_PRIORITIZED') : JText::_('WISHLIST_TITLE_RECENT_WISHES');
				if (count($wishlist->items) > 0 && $items > $filters['limit']) 
				{
					$title.= ' (<a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a
						.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid)
						.'">'.JText::_('view all') .' '.$items.'</a>)';
				} 
				else 
				{
					$title .= ' ('.$items.')';
				}
				
				// HTML output
				// Instantiate a view
				ximport('Hubzero_Plugin_View');
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'publications',
						'element'=>'wishlist',
						'name'=>'browse'
					)
				);
	
				// Pass the view some info
				$view->option = $option;
				$view->publication = $publication;
				$view->title = $title;
				$view->wishlist = $wishlist;
				$view->filters = $filters;
				$view->admin = $admin;
				$view->config = $this->config;
				if ($this->getError()) 
				{
					$view->setError( $this->getError() );
				}
	
				// Return the output
				$html = $view->loadTemplate();
			}						
		}
					
		// Build the HTML meant for the "about" tab's metadata overview
		$metadata = '';
		
		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
				$metadata  = '<p class="wishlist"><a href="'.JRoute::_('index.php?option=com_publications'.a.'id='.
					$publication->id.a.'active=wishlist').'">'.JText::sprintf('NUM_WISHES',$items);
				$metadata .= '</a> (<a href="'.JRoute::_('index.php?option='.$option.a.'id='.
					$id.a.'task=add').'">'.JText::_('ADD_NEW_WISH').'</a>)</p>'.n;
		}
		
		$arr = array(
				'html'=>$html,
				'metadata'=>$metadata
			);

		return $arr;
	}
}