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
 * Resources Plugin class for favoriting a resource
 */
class plgResourcesFavorite extends JPlugin
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
	public function onResourcesAreas($resource)
	{
		return array();
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
	public function onResources($resource, $option, $areas, $rtrn='all')
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($resource))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($resource)))) 
			{
				$rtrn = 'metadata';
			}
		}

		// Incoming action
		$action = JRequest::getVar('action', '');
		if ($action && $action == 'favorite') 
		{
			// Check the user's logged-in status
			$this->fav($resource->id);
		}

		$arr = array(
			'area' => 'favorite',
			'html' => '',
			'metadata' => ''
		);

		// Build the HTML meant for the "about" tab's metadata overview
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) 
		{
			if ($rtrn == 'all' || $rtrn == 'metadata') 
			{
				// Push some scripts to the template
				ximport('Hubzero_Document');
				Hubzero_Document::addPluginScript('resources', 'favorite');

				ximport('Hubzero_Favorite');
				if (!class_exists('Hubzero_Favorite')) 
				{
					return $arr;
				}

				$database =& JFactory::getDBO();

				$fav = new Hubzero_Favorite($database);
				$fav->loadFavorite($juser->get('id'), $resource->id, 'resources');
				if (!$fav->id) 
				{
					$txt = JText::_('PLG_RESOURCES_FAVORITES_FAVORITE_THIS');
					$cls = '';
				} 
				else 
				{
					$txt = JText::_('PLG_RESOURCES_FAVORITES_UNFAVORITE_THIS');
					$cls = 'faved';
				}

				ximport('Hubzero_Plugin_View');
				$view = new Hubzero_Plugin_View(
					array(
						'folder'  => 'resources',
						'element' => 'favorite',
						'name'    => 'metadata'
					)
				);
				$view->cls = $cls;
				$view->txt = $txt;
				$view->option = $option;
				$view->resource = $resource;
				$arr['metadata'] = $view->loadTemplate();
			}
		}

		return $arr;
	}

	/**
	 * Set an item's favorite status
	 * 
	 * @param      string $option Component name
	 * @return     void
	 */
	public function onResourcesFavorite($option)
	{
		$rid = JRequest::getInt('rid', 0);

		$arr = array('html' => '');
		if ($rid) 
		{
			$arr['html'] = $this->fav($rid);
		}
		return $arr;
	}

	/**
	 * Un/favorite an item
	 * 
	 * @param      integer $oid Resource to un/favorite
	 * @return     void
	 */
	public function fav($oid)
	{
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) 
		{
			ximport('Hubzero_Favorite');

			$database =& JFactory::getDBO();

			$fav = new Hubzero_Favorite($database);
			$fav->loadFavorite($juser->get('id'), $oid, 'resources');

			if (!$fav->id) 
			{
				$fav->uid = $juser->get('id');
				$fav->oid = $oid;
				$fav->tbl = 'resources';
				$fav->faved = date('Y-m-d H:i:s');
				$fav->check();
				$fav->store();

				return JText::_('PLG_RESOURCES_FAVORITES_UNFAVORITE_THIS');
			} 
			else 
			{
				$fav->delete();

				return JText::_('PLG_RESOURCES_FAVORITES_FAVORITE_THIS');
			}
		}
	}
}
