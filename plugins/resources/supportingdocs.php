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
JPlugin::loadLanguage( 'plg_resources_supportingdocs' );
	
//-----------

class plgResourcesSupportingDocs extends JPlugin
{
	public function plgResourcesSupportingDocs(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'supportingdocs' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onResourcesAreas( $resource, $archive = 0 ) 
	{
		if ($archive) {
			$areas = array();			
		} else if ($resource->_type->_params->get('plg_supportingdocs')) {
			$areas = array(
				'supportingdocs' => JText::_('PLG_RESOURCES_SUPPORTINGDOCS')
			);
		} else {
			$areas = array();
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
				// do nothing
			}
		}
		
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('resources', 'supportingdocs');
		
		$database =& JFactory::getDBO();
		
		// Initiate a resource helper class
		$helper = new ResourcesHelper( $resource->id, $database );
		//$excludeFirstChild = $resource->type == 7 ? 0 : 1;
		$helper->getChildren( $resource->id, 0, 'all', 0 );
		
		$config =& JComponentHelper::getParams( $option );

		$xhub =& Hubzero_Factory::getHub();
		
		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'resources',
				'element'=>'supportingdocs',
				'name'=>'browse'
			)
		);

		// Pass the view some info
		$view->option = $option;
		$view->resource = $resource;
		$view->helper = $helper;
		$view->config = $config;
		$view->live_site = $xhub->getCfg('hubLongURL');
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}
