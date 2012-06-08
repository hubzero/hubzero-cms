<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
class modLatestDiscussions extends JObject
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

		ximport("Hubzero_Group");

		//get the params
		$this->cls = $this->params->get('moduleclass_sfx');
		$this->limit = $this->params->get('limit', 5);
		$this->charlimit = $this->params->get('charlimit', 100);
		$this->feedlink = $this->params->get('feedlink', 'yes');
		$this->morelink = $this->params->get('morelink', '');
		$include = $this->params->get('forum', 'both');

		//get all forum posts on site forum
		$database->setQuery("SELECT f.* FROM #__forum_posts f WHERE f.group_id='0' AND f.parent='0'");
		$site_forum = $database->loadAssocList();

		//get any group posts
		$database->setQuery("SELECT f.* FROM #__forum_posts f WHERE f.group_id<>'0' AND f.parent='0'");
		$group_forum = $database->loadAssocList();

		//make sure that the group for each forum post has the right privacy setting
		foreach ($group_forum as $k => $gf) 
		{
			$group = Hubzero_Group::getInstance($gf['group_id']);
			if (is_object($group)) 
			{
				$forum_access = $group->getPluginAccess("forum");
				
				if ($forum_access == 'nobody' 
				 || ($forum_access == 'registered' && $juser->get('guest')) 
				 || ($forum_access == 'members' && !in_array($juser->get('id'), $group->get('members')))) 
				{
					unset($group_forum[$k]);
				}
			} 
			else 
			{
				unset($group_forum[$k]);
			}
		}
		
		//based on param decide what to include
		switch ($include) 
		{
			case 'site':  $posts = $site_forum;  break;
			case 'group': $posts = $group_forum; break;
			case 'both':  
			default:
				$posts = array_merge($site_forum, $group_forum);
			break;
		}

		//function to sort by created date
		function sortbydate($a, $b)
		{
			$d1 = date("Y-m-d H:i:s", strtotime($a['created']));
			$d2 = date("Y-m-d H:i:s", strtotime($b['created']));
			
			return ($d1 > $d2) ? -1 : 1;
		}

		//sort using function above - date desc
		usort($posts, "sortbydate");

		//set posts to view
		$this->posts = $posts;

		// Push the module CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
