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

/**
 * Resources Plugin class for about tab of tools
 */
class plgResourcesAbouttool extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesAreas($model)
	{
		if ($model->type->params->get('plg_abouttool', 0)) 
		{
			$areas = array(
				'about' => JText::_('PLG_RESOURCES_ABOUT')
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
			'area' => 'about',
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

		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			\Hubzero\Document\Assets::addPluginStyleSheet('resources', 'abouttool');

			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'resources',
					'element' => 'abouttool',
					'name'    => 'index'
				)
			);
			$view->option     = $option;
			$view->model      = $model;
			//$view->authorized = $resource->authorized;
			$view->database   = JFactory::getDBO();
			$view->juser      = JFactory::getUser();

			/*if (!$view->juser->get('guest')) 
			{
				$xgroups = \Hubzero\User\Helper::getGroups($view->juser->get('id'), 'all');
				// Get the groups the user has access to
				$view->usersgroups = $this->_getUsersGroups($xgroups);
			} 
			else 
			{
				$view->usersgroups = array();
			}

			$paramsClass = 'JRegistry';
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				$paramsClass = 'JParameter';
			}

			$view->attribs = new $paramsClass($resource->attribs);
			$view->config  = JComponentHelper::getParams($option);

			$rparams = new $paramsClass($resource->params);
			$params = $view->config;
			$params->merge($rparams);

			$view->params   = $params;
			$view->plugin   = $this->params;
			$view->helper   = new ResourcesHelper($resource->id, $view->database);*/
			$view->thistool = $model->thistool;
			$view->curtool  = $model->curtool;
			$view->alltools = $model->alltools;
			$view->revision = $model->revision;
			$view->params   = $this->params;

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Create an array of just group aliases
	 * 
	 * @param      array $groups A list of groups
	 * @return     array
	 */
	private function _getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) 
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) 
				{
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}
}