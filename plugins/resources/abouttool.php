<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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
defined('_JEXEC') or die('Restricted access');

//-----------

jimport('joomla.plugin.plugin');
JPlugin::loadLanguage('plg_resources_abouttool');

//-----------

class plgResourcesAbouttool extends JPlugin
{
	public function plgResourcesAbouttool(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin('resources', 'abouttool');
		$this->_params = new JParameter($this->_plugin->params);
	}

	//-----------

	public function &onResourcesAreas($resource) 
	{
		if ($resource->_type->_params->get('plg_abouttool', 0)) {
			$areas = array(
				'about' => JText::_('PLG_RESOURCES_ABOUT')
			);
		} else {
			$areas = array();
		}
		return $areas;
	}

	//-----------

	//public function onResources($resource, $authorized, $option, $areas, $rtrn='all', $data=array())
	public function onResources($resource, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) {
			if (!array_intersect($areas, $this->onResourcesAreas($resource)) 
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($resource)))) {
				$rtrn = 'metadata';
			}
		}

		if ($rtrn == 'all' || $rtrn == 'html') {
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'abouttool',
					'name'    => 'index'
				)
			);
			$view->option     = $option;
			$view->resource   = $resource;
			$view->authorized = $resource->authorized;
			$view->database   = JFactory::getDBO();
			$view->juser      = JFactory::getUser();

			if (!$view->juser->get('guest')) {
				ximport('Hubzero_User_Helper');
				$xgroups = Hubzero_User_Helper::getGroups($view->juser->get('id'), 'all');
				// Get the groups the user has access to
				$view->usersgroups = $this->_getUsersGroups($xgroups);
			} else {
				$view->usersgroups = array();
			}

			$view->attribs = new JParameter($resource->attribs);
			$view->config  = JComponentHelper::getParams($option);

			$rparams = new JParameter($resource->params);
			$params = $view->config;
			$params->merge($rparams);

			$view->params   = $params;
			$view->helper   = new ResourcesHelper($resource->id, $view->database);
			$view->thistool = $resource->thistool;
			$view->curtool  = $resource->curtool;
			$view->alltools = $resource->alltools;
			$view->revision = $resource->revision;

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	private function _getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups)) {
			foreach ($groups as $group)
			{
				if ($group->regconfirmed) {
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}
}