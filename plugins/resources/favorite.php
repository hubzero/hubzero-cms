<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_favorite' );
	
//-----------

class plgResourcesFavorite extends JPlugin
{
	public function plgResourcesFavorite(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'favorite' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function &onResourcesAreas( $resource ) 
	{
		$areas = array();
		return $areas;
	}

	//-----------

	public function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		// Incoming action
		$action = JRequest::getVar( 'action', '' );
		if ($action && $action == 'favorite') {
			// Check the user's logged-in status
			$this->fav( $resource->id );
		}
		
		$arr = array(
				'html'=>'',
				'metadata'=>''
			);
		
		// Build the HTML meant for the "about" tab's metadata overview
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			if ($rtrn == 'all' || $rtrn == 'metadata') {
				// Push some scripts to the template
				if (is_file(JPATH_ROOT.DS.'plugins'.DS.'resources'.DS.'favorite'.DS.'favorite.js')) {
					$document =& JFactory::getDocument();
					$document->addScript('plugins'.DS.'resources'.DS.'favorite'.DS.'favorite.js');
				}
				
				ximport('Hubzero_Favorite');
				if (!class_exists('Hubzero_Favorite')) {
					return $arr;
				}
				
				$database =& JFactory::getDBO();
				
				$fav = new Hubzero_Favorite( $database );
				$fav->loadFavorite( $juser->get('id'), $resource->id, 'resources' );
				if (!$fav->id) {
					$txt = JText::_('PLG_RESOURCES_FAVORITES_FAVORITE_THIS');
					$cls = '';
				} else {
					$txt = JText::_('PLG_RESOURCES_FAVORITES_UNFAVORITE_THIS');
					$cls = 'faved';
				}
				
				$arr['metadata'] = '<p class="favorite"><a id="fav-this" class="'.$cls.'" href="'.JRoute::_('index.php?option='.$option.'&id='.$resource->id.'&action=favorite').'">'.$txt.'</a></p>';
			}
		}

		return $arr;
	}
	
	//-----------
	
	public function onResourcesFavorite( $option ) 
	{
		$rid = JRequest::getInt( 'rid', 0 );
		
		if ($rid) {
			$this->fav( $rid );
		}
	}
	
	//-----------
	
	public function fav( $oid ) 
	{
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			ximport('Hubzero_Favorite');

			$database =& JFactory::getDBO();

			$fav = new Hubzero_Favorite( $database );
			$fav->loadFavorite( $juser->get('id'), $oid, 'resources' );

			if (!$fav->id) {
				$fav->uid = $juser->get('id');
				$fav->oid = $oid;
				$fav->tbl = 'resources';
				$fav->faved = date( 'Y-m-d H:i:s');
				$fav->check();
				$fav->store();
				
				echo JText::_('PLG_RESOURCES_FAVORITES_UNFAVORITE_THIS');
			} else {
				$fav->delete();
				
				echo JText::_('PLG_RESOURCES_FAVORITES_FAVORITE_THIS');
			}
		}
	}
}
