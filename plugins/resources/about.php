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
JPlugin::loadLanguage('plg_resources_about');

/**
 * Short description for 'plgResourcesAbout'
 * 
 * Long description (if any) ...
 */
class plgResourcesAbout extends JPlugin
{

	/**
	 * Short description for 'plgResourcesAbout'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgResourcesAbout(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin('resources', 'about');
		$this->_params = new JParameter($this->_plugin->params);
	}

	/**
	 * Short description for 'onResourcesAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $resource Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function &onResourcesAreas($resource)
	{
		if ($resource->_type->_params->get('plg_about', 0)) {
			$areas = array(
				'about' => JText::_('PLG_RESOURCES_ABOUT')
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
	 * @param      object $resource Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @param      string $rtrn Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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

		$ar = $this->onResourcesAreas($resource);
		if (empty($ar)) {
			$rtrn = '';
		}

		if ($rtrn == 'all' || $rtrn == 'html') {
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => 'about',
					'name'    => 'index'
				)
			);
			$view->option = $option;
			$view->resource = $resource;
			$view->authorized = $resource->authorized;
			$view->database = JFactory::getDBO();
			$view->juser = JFactory::getUser();

			if (!$view->juser->get('guest')) {
				ximport('Hubzero_User_Helper');
				$xgroups = Hubzero_User_Helper::getGroups($view->juser->get('id'), 'all');
				// Get the groups the user has access to
				$view->usersgroups = $this->_getUsersGroups($xgroups);
			} else {
				$view->usersgroups = array();
			}

			$view->attribs = new JParameter($resource->attribs);
			$view->config = JComponentHelper::getParams($option);

			$rparams = new JParameter($resource->params);
			$params = $view->config;
			$params->merge($rparams);

			$view->params = $params;
			$view->plugin = $this->_params;
			$view->helper = new ResourcesHelper($resource->id, $view->database);

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Short description for '_getUsersGroups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $groups Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
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