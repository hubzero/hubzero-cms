<?php
/**
 * @package     hubzero-cms
 * @author      HUBzero
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 * All rights reserved.
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

/**
 * Module class for displaying the latest forum posts
 */
class modLatestGroups extends JObject
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $this->params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function display()
	{
		$database =& JFactory::getDBO();

		$juser =& JFactory::getUser();
		$uid = $juser->get('id');

		ximport("Hubzero_Group");
		ximport("Hubzero_Group_Helper");

		//get the params
		$this->cls = $this->params->get('moduleclass_sfx');
		$this->limit = $this->params->get('limit', 5);
		$this->charlimit = $this->params->get('charlimit', 100);
		$this->feedlink = $this->params->get('feedlink', 'yes');
		$this->morelink = $this->params->get('morelink', '');
		
		// Get popular groups
		$popularGroups = Hubzero_Group_Helper::getPopularGroups();

		$counter = 0;
		$groupsToDisplay = array();
		foreach ($popularGroups as $g)
		{
			// Get the group
			$group = Hubzero_Group::getInstance($g->gidNumber);
			
			// Check join policy
			$joinPolicy = $group->get('join_policy');
			
			// If group is invite only or closed check if th user is a member of the group
			if ($joinPolicy > 1)
			{
				// if not a member do not display the group
				if (!$group->isMember($uid))
				{
					continue;
				}
			}
			
			$groupsToDisplay[] = $g;
			
			$counter++;
			if ($counter == $this->limit)
			{
				break;
			}
		}
		
		//set groups to view
		$this->groups = $groupsToDisplay;

		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
