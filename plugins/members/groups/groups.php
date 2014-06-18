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

/**
 * Members Plugin class for groups
 */
class plgMembersGroups extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

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
			$areas['icon'] = 'f042';
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

		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		$applicants = $member->getGroups('applicants');
		$invitees   = $member->getGroups('invitees');
		$members    = $member->getGroups('members');
		$managers   = $member->getGroups('managers');

		$applicants = (is_array($applicants)) ? $applicants : array();
		$invitees   = (is_array($invitees))   ? $invitees   : array();
		$members    = (is_array($members))    ? $members    : array();
		$managers   = (is_array($managers))   ? $managers   : array();

		$groups = array_merge($applicants, $invitees);
		$managerids = array();
		foreach ($managers as $manager)
		{
			$groups[$manager->description] = $manager;
			$managerids[] = $manager->cn;
		}
		foreach ($members as $mem)
		{
			if (!in_array($mem->cn, $managerids))
			{
				$groups[$mem->description] = $mem;
			}
		}
		ksort($groups);

		// Build the final HTML
		if ($returnhtml)
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'summary'
				)
			);
			$view->total  = count($groups);
			$view->filter = strtolower(JRequest::getWord('filter', '', 'get'));

			if (in_array($view->filter, array('invitees', 'applicants', 'members', 'managers')))
			{
				$g = array();
				//$view->groups = $member->getGroups($view->filter);
				switch ($view->filter)
				{
					case 'invitees':
						foreach ($groups as $key => $group)
						{
							if (!$group->registered && $group->regconfirmed)
							{
								$g[$key] = $group;
							}
						}
					break;

					case 'applicants':
						foreach ($groups as $key => $group)
						{
							if ($group->registered && !$group->regconfirmed)
							{
								$g[$key] = $group;
							}
						}
					break;

					case 'members':
						foreach ($groups as $key => $group)
						{
							if ($group->registered && $group->regconfirmed && !$group->manager)
							{
								$g[$key] = $group;
							}
						}
					break;

					case 'managers':
						foreach ($groups as $key => $group)
						{
							if ($group->manager)
							{
								$g[$key] = $group;
							}
						}
					break;
				}
				$groups = $g;
			}

			$view->groups = $groups;
			$view->member = $member;
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
					$title = JText::sprintf('PLG_MEMBERS_GROUPS_NEW_INVITATIONS', count($invitees));
					$link = JRoute::_('index.php?option=com_members&id=' . $member->get('uidNumber') . '&active=groups&filter=invitees');
					$arr['metadata']['alert'] = '<a class="alrt" href="' . $link . '"><span><h5>' . JText::_('PLG_MEMBERS_GROUPS_ALERT') . '</h5>' . $title . '</span></a>';
				}
			}
		}

		return $arr;
	}
}
