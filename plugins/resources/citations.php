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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_citations' );

/**
 * Short description for 'plgResourcesCitations'
 * 
 * Long description (if any) ...
 */
class plgResourcesCitations extends JPlugin
{

	/**
	 * Short description for 'plgResourcesCitations'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgResourcesCitations(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'citations' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onResourcesAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $resource Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function &onResourcesAreas( $resource )
	{
		if ($resource->_type->_params->get('plg_citations')) {
			$areas = array(
				'citations' => JText::_('PLG_RESOURCES_CITATIONS')
			);
		} else {
			$areas = array();
		}
		
		return $areas;
	}

	/**
	 * Short description for 'onResources'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $resource Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @param      string $rtrn Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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
		if (!$resource->_type->_params->get('plg_citations')) {
			return $arr;
		}

		$database =& JFactory::getDBO();

		// Get a needed library
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'citation.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'association.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'author.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'secondary.php' );

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

