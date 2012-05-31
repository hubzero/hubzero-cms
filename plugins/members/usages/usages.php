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
 * Members Plugin class for usages
 */
class plgMembersUsages extends JPlugin
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
	 * Event call to determine if this plugin should return data
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function &onMembersAreas($user, $member) 
	{
		$areas = $this->_authorize() ? array('usages' => JText::_('PLG_MEMBERS_USAGES')) : array();
		return $areas;
	}

	/**
	 * Check authorization level
	 * 
	 * @return     boolean True if authorized
	 */
	private function _authorize()
	{
		// Check if the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) 
		{
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if ($juser->authorize('com_members', 'manage')) 
		{
			return true;
		}

		// Get the groups of the current logged-in user
		//ximport('Hubzero_User_Helper');
		//$ugs = Hubzero_User_Helper::getGroups($juser->get('id'));
		$ugs = $this->member->getGroups('all');

		// Get the groups that are allowed to view this plugin
		$groups = trim($this->params->get('groups'));
		if (strstr(',', $groups)) 
		{
			$groups = explode(',', $groups);
			if (is_array($groups)) 
			{
				$groups = array_map('trim', $groups);
			}
		} 
		else 
		{
			$groups = array($groups);
		}

		if ($ugs && is_array($ugs)) 
		{
			// See if the user is any of the groups this plugin is restricted to
			foreach ($ugs as $u)
			{
				if (in_array($u->cn, $groups)) 
				{
					return true;
					break;
				}
			}
		}
		return false;
	}

	/**
	 * Event call to return data for a specific member
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onMembers($member, $option, $authorized, $areas)
	{
		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onMembersAreas($authorized))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($authorized)))) 
			{
				return $arr;
			}
		}
		
		$this->member = $member;

		if (!$this->_authorize()) 
		{
			return $arr;
		}

		$arr['html'] = '<p class="passed">You can see this.</p>';

		return $arr;
	}
}

