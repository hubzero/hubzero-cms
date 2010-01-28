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
	function plgResourcesSupportingDocs(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'supportingdocs' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onResourcesAreas( $resource, $archive = 0 ) 
	{
		
		if($archive) {
			$areas = array();			
		}	
			
		//else if ($resource->type !=8 && $resource->type != 31 && $resource->type != 2 && $resource->type != 6 ) {
		else if ($resource->type !=8) {
			$areas = array(
				'supportingdocs' => JText::_('Supporting Documents')
			);
		} else {
			$areas = array();			
		}
		
		return $areas;
	}

	//-----------

	function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				// do nothing
			}
		}
		
		$database =& JFactory::getDBO();
		
		// Initiate a resource helper class
		$helper = new ResourcesHelper( $resource->id, $database );
		$config =& JComponentHelper::getParams( $option );
		
		$helper->getChildren( $resource->id, 0, 'all', 1 );
		$children = $helper->children;
		$dls = '';
		
		$xhub =& XFactory::getHub();
		$live_site = $xhub->getCfg('hubLongURL');
		
		switch ($resource->type)
			{
				case 7:
					$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $helper->children, '', '', '', $resource->id, $fsize=0 );									
				break;
					
				case 4:					
					$dls = ResourcesHtml::writeDownloads( $database, $resource->id, $option, $config, $fsize=0 );
				break;
					
				case 8:
					// show no docs
				break;
				
				case 6:
				case 31:
				case 2:					
					$helper->getChildren( $resource->id, 0, 'no' );
					$children = $helper->children;
					$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $children, $live_site, '', '', $resource->id, $fsize=0 );
				break;
					
				default:
					$dls = ResourcesHtml::writeChildren( $config, $option, $database, $resource, $helper->children, '', '', '', $resource->id, $fsize=0 );
				break;
          }
		
		$html = '';
		$html .= ResourcesHtml::supportingDocuments($dls, 1);

		$arr = array(
				'html'=>$html,
				'metadata'=>''
		);

		return $arr;
	}
}
