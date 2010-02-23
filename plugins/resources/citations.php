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
JPlugin::loadLanguage( 'plg_resources_citations' );
	
//-----------

class plgResourcesCitations extends JPlugin
{
	public function plgResourcesCitations(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'citations' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onResourcesAreas( $resource )
	{
		$areas = array(
			'citations' => JText::_('PLG_RESOURCES_CITATIONS')
		);
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
		
		$database =& JFactory::getDBO();

		// Get a needed library
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'citations.class.php');

		// Get reviews for this resource
		$c = new CitationsCitation( $database );
		$citations = $c->getCitations( 'resource', $resource->id );
		
		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') {
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'citations',
					'name'=>'browse'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->resource = $resource;
			$view->citations = $citations;
			$view->format = ($this->_params->get('format')) ? $this->_params->get('format') : 'APA';
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			if ($resource->alias) {
				$url = JRoute::_('index.php?option='.$option.'&alias='.$resource->alias.'&active=citations');
			} else {
				$url = JRoute::_('index.php?option='.$option.'&id='.$resource->id.'&active=citations');
			}
			
			$arr['metadata']  = '<p class="citation"><a href="'.$url.'">'.JText::sprintf('PLG_RESOURCES_CITATIONS_COUNT',count($citations)).'</a></p>';
		}

		// Return results
		return $arr;
	}
}
