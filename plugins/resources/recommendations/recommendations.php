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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Resources Plugin class for recommendations
 */
class plgResourcesRecommendations extends JPlugin
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

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesSubAreas($resource)
	{
		$areas = array(
			'recommendations' => JText::_('PLG_RESOURCES_RECOMMENDATIONS')
		);
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 * 
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      integer $miniview  View style
	 * @return     array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area' => 'recommendations',
			'html' => '',
			'metadata' => ''
		);

		// Get some needed libraries
		include_once(JPATH_ROOT . DS . 'plugins' . DS . 'resources' . DS . 'recommendations' . DS . 'resources.recommendation.php');

		// Set some filters for returning results
		$filters = array();
		$filters['id'] = $resource->id;
		$filters['threshold'] = $this->params->get('threshold');
		$filters['threshold'] = ($filters['threshold']) ? $filters['threshold'] : '0.21';
		$filters['limit'] = $this->params->get('display_limit');
		$filters['limit'] = ($filters['limit']) ? $filters['limit'] : 10;

		// Get recommendations
		$database =& JFactory::getDBO();
		$r = new ResourcesRecommendation($database);
		$results = $r->getResults($filters);

		// Instantiate a view
		ximport('Hubzero_Plugin_View');
		if ($miniview) 
		{
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'recommendations',
					'name'    => 'browse',
					'layout'  => 'mini'
				)
			);
		} 
		else 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginScript('resources', 'recommendations');

			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'recommendations',
					'name'    => 'browse'
				)
			);
		}

		// Pass the view some info
		$view->option = $option;
		$view->resource = $resource;
		$view->results = $results;
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}

