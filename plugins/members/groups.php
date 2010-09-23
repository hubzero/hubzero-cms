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
JPlugin::loadLanguage( 'plg_members_groups' );

//-----------

class plgMembersGroups extends JPlugin
{
	public function plgMembersGroups(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'groups' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onMembersAreas( $authorized )
	{
		if (!$authorized) {
			$areas = array();
		} else {
			$areas = array(
				'groups' => JText::_('PLG_MEMBERS_GROUPS')
			);
		}

		return $areas;
	}

	//-----------

	public function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		$returnmeta = true;
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		if (!$authorized) {
			$returnhtml = false;
			$returnmeta = false;
		}

		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		ximport('Hubzero_User_Helper');
		$applicants = Hubzero_User_Helper::getGroups( $member->get('uidNumber'), 'applicants', 1 );
		$invitees = Hubzero_User_Helper::getGroups( $member->get('uidNumber'), 'invitees', 1 );
		$members = Hubzero_User_Helper::getGroups( $member->get('uidNumber'), 'members', 1 );
		$managers = Hubzero_User_Helper::getGroups( $member->get('uidNumber'), 'managers', 1 );
		
		$applicants = (is_array($applicants)) ? $applicants : array();
		$invitees = (is_array($invitees)) ? $invitees : array();
		$members = (is_array($members)) ? $members : array();
		$managers = (is_array($managers)) ? $managers : array();
		
		$groups = array_merge($applicants, $invitees);
		$managerids = array();
		foreach ($managers as $manager) 
		{
			$groups[] = $manager;
			$managerids[] = $manager->cn;
		}
		foreach ($members as $mem) 
		{
			if (!in_array($mem->cn,$managerids)) {
				$groups[] = $mem;
			}
		}

		// Build the final HTML
		if ($returnhtml) {
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'members',
					'element'=>'groups',
					'name'=>'summary'
				)
			);
			$view->authorized = $authorized;
			$view->groups = $groups;
			$view->option = 'com_groups';
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			
			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		if ($returnmeta) {
			$arr['metadata'] = '<p class="groups"><a href="'.JRoute::_('index.php?option='.$option.'&id='.$member->get('uidNumber').'&active=groups').'">'.JText::sprintf('PLG_MEMBERS_GROUPS_NUMBER_GROUPS',count($groups)).'</a></p>'."\n";
		}

		return $arr;
	}
}