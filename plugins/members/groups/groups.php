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
 * Members Plugin class for groups
 */
class plgMembersGroups extends JPlugin
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
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('uidNumber'))
		{
			$areas['groups'] = JText::_('PLG_MEMBERS_GROUPS');
		}

		return $areas;
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
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member)))) 
			{
				$returnhtml = false;
			}
		}

		/*
		if (!$authorized) {
			$returnhtml = false;
			$returnmeta = false;
		}
		*/

		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		$filter = JRequest::getVar("filter","","get");

		ximport('Hubzero_User_Helper');
		$applicants = $member->getGroups('applicants'); //Hubzero_User_Helper::getGroups($member->get('uidNumber'), 'applicants', 1);
		$invitees   = $member->getGroups('invitees'); //Hubzero_User_Helper::getGroups($member->get('uidNumber'), 'invitees', 1);
		$members    = $member->getGroups('members'); //Hubzero_User_Helper::getGroups($member->get('uidNumber'), 'members', 1);
		$managers   = $member->getGroups('managers'); //Hubzero_User_Helper::getGroups($member->get('uidNumber'), 'managers', 1);

		$applicants = (is_array($applicants)) ? $applicants : array();
		$invitees   = (is_array($invitees))   ? $invitees   : array();
		$members    = (is_array($members))    ? $members    : array();
		$managers   = (is_array($managers))   ? $managers   : array();

		$groups = array_merge($applicants, $invitees);
		$managerids = array();
		foreach ($managers as $manager)
		{
			$groups[] = $manager;
			$managerids[] = $manager->cn;
		}
		foreach ($members as $mem)
		{
			if (!in_array($mem->cn,$managerids)) 
			{
				$groups[] = $mem;
			}
		}

		// Build the final HTML
		if ($returnhtml) 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'groups');

			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => 'groups',
					'name'    => 'summary'
				)
			);

			$view->groups = $groups;
			if (in_array($filter, array('invitees', 'applicants', 'members', 'managers')))
			{
				$view->groups = $member->getGroups($filter);
			}

			$view->option = 'com_groups';
			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}

			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		if ($returnmeta) 
		{
			//display a different message if its me
			if ($member->get('uidNumber') == $user->get("id")) 
			{ 
				$arr['metadata']['count'] = count($groups);

				if (count($invitees))
				{
					$title = count($invitees) . ' new group invitation(s)';
					$link = JRoute::_('index.php?option=com_members&id=' . $member->get('uidNumber') . '&active=groups&filter=invitees');
					$arr['metadata']['alert'] = '<a class="alrt" href="' . $link . '"><span><h5>Groups Alert</h5>' . $title . '</span></a>';
				}
			}
		}

		return $arr;
	}
}
