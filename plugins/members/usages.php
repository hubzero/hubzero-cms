<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_usages' );

//-----------

class plgMembersUsages extends JPlugin
{
	public function plgMembersUsages(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'usages' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onMembersAreas( $authorized ) 
	{
		if (!$this->_authorize()) {
			return array();
		}
		
		$areas = array(
			'usages' => JText::_('PLG_MEMBERS_USAGES')
		);
		
		return $areas;
	}
	
	private function _authorize() 
	{
		// Check if the user is logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}

		// Check if they're a site admin (from Joomla)
		if ($juser->authorize('com_members', 'manage')) {
			return true;
		}

		// Get the groups of the current logged-in user
		ximport('xuserhelper');
		$ugs = XUserHelper::getGroups( $juser->get('id') );

		// Get the groups that are allowed to view this plugin
		$groups = trim($this->_params->get('groups'));
		if (strstr(',',$groups)) {
			$groups = explode(',',$groups);
			if (is_array($groups)) {
				$groups = array_map('trim',$groups);
			}
		} else {
			$groups = array($groups);
		}
		
		if ($ugs && is_array($ugs)) {
			// See if the user is any of the groups this plugin is restricted to
			foreach ($ugs as $u) 
			{
				if (in_array($u->cn, $groups)) {
					return true;
					break;
				}
			}
		}
		return false;
	}
	
	//-----------

	public function onMembers( $member, $option, $authorized, $areas )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				return $arr;
			}
		}
		
		if (!$this->_authorize()) {
			return $arr;
		}
		
		$arr['html'] = '<p class="passed">You can see this.</p>';

		return $arr;
	}
}
