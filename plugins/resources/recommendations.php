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
JPlugin::loadLanguage( 'plg_resources_recommendations' );
	
//-----------

class plgResourcesRecommendations extends JPlugin
{
	public function plgResourcesRecommendations(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'recommendations' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onResourcesSubAreas( $resource )
	{
		$areas = array(
			'recommendations' => JText::_('PLG_RESOURCES_RECOMMENDATIONS')
		);
		return $areas;
	}

	//-----------

	public function onResourcesSub( $resource, $option, $miniview=0 )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Check if they are logged in
		//$juser =& JFactory::getUser();
		//if ($juser->get('guest')) {
		//	return $arr;
		//}
		
		// Check if they're a site admin (from Joomla)
		//if (!$juser->authorize($option, 'manage')) {
		//	return $arr;
		//}
		
		// Get some needed libraries
		include_once(JPATH_ROOT.DS.'plugins'.DS.'resources'.DS.'recommendations'.DS.'resources.recommendation.php');
		
		// Set some filters for returning results
		$filters = array();
		$filters['id'] = $resource->id;
		$filters['threshold'] = $this->_params->get('threshold');
		$filters['threshold'] = ($filters['threshold']) ? $filters['threshold'] : '0.21';
		$filters['limit'] = $this->_params->get('display_limit');
		$filters['limit'] = ($filters['limit']) ? $filters['limit'] : 10;
		
		// Get recommendations
		$database =& JFactory::getDBO();
		$r = new ResourcesRecommendation($database);
		$results = $r->getResults($filters);
		
		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		if ($miniview) {
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'recommendations',
					'name'=>'browse',
					'layout'=>'mini'
				)
			);
		} else {
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'recommendations',
					'name'=>'browse'
				)
			);
		}

		// Pass the view some info
		$view->option = $option;
		$view->resource = $resource;
		$view->results = $results;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}
