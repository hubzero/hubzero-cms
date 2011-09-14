<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_supportingdocs' );

class plgResourcesSupportingDocs extends JPlugin
{
	public function plgResourcesSupportingDocs(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'supportingdocs' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

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
				return;
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

