<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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