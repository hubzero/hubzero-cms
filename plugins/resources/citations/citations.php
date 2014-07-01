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
 * Resources Plugin class for citations
 */
class plgResourcesCitations extends JPlugin
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
	public function &onResourcesAreas($model)
	{
		if ($model->type->params->get('plg_citations')
			&& $model->access('view-all')) 
		{
			$areas = array(
				'citations' => JText::_('PLG_RESOURCES_CITATIONS')
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
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area' => 'citations',
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model)))) 
			{
				$rtrn = 'metadata';
			}
		}
		if (!$model->type->params->get('plg_citations')) 
		{
			return $arr;
		}

		$database = JFactory::getDBO();

		// Get a needed library
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');

		// Get reviews for this resource
		$c = new CitationsCitation($database);
		$citations = $c->getCitations('resource', $model->resource->id);

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'resources',
					'element' => 'citations',
					'name'    => 'browse'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->resource = $model->resource;
			$view->citations = $citations;
			$view->format = $this->params->get('format', 'APA');
			if ($this->getError()) 
			{
				$view->setError($this->getError());
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'resources',
					'element' => 'citations',
					'name'    => 'metadata'
				)
			);

			if ($model->resource->alias) 
			{
				$url = JRoute::_('index.php?option=' . $option . '&alias=' . $model->resource->alias . '&active=citations');
			} 
			else 
			{
				$url = JRoute::_('index.php?option=' . $option . '&id=' . $model->resource->id . '&active=citations');
			}

			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'resources',
					'element' => 'citations',
					'name'    => 'metadata'
				)
			);
			$view->url = $url;
			$view->citations = $citations;
			$arr['metadata'] = $view->loadTemplate();
		}

		// Return results
		return $arr;
	}
}

