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
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_groups' );

class plgMembersGroups extends JPlugin
{
	public function plgMembersGroups(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'groups' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

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
		$applicants = Hubzero_User_Helper::getGroups( $member->get('uidNumber'), 'applicants', 1);
		$invitees = Hubzero_User_Helper::getGroups( $member->get('uidNumber'), 'invitees', 1);
		$members = Hubzero_User_Helper::getGroups( $member->get('uidNumber'), 'members', 1);
		$managers = Hubzero_User_Helper::getGroups( $member->get('uidNumber'), 'managers', 1);

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
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'groups');

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
