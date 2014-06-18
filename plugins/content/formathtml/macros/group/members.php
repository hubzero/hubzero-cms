<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Formathtml\Macros\Group;

require_once JPATH_ROOT.'/plugins/content/formathtml/macros/group.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;

/**
 * Group events Macro
 */
class Members extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Displays group members.</p>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Group.Members()]]</code></li>
								<li><code>[[Group.Members(3, random)]]</code> - Displays 3 group members</li>
							</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		// check if we can render
		if (!parent::canRender())
		{
			return \JText::_('[This macro is designed for Groups only]');
		}

		// get args
		$args = $this->getArgs();

		//array of filters
		$filters = array(
			'limit' => (count($args) == 1 && is_numeric($args[0])) ? $args[0] : 12
		);

		// get members
		$members = $this->getGroupMembers($this->group, $filters);

		//are we a group member
		$isMember = (in_array(\JFactory::getUser()->get('id'), $this->group->get('members'))) ? true : false;

		//get the members plugin access for this group
		$memberAccess = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'members');

		// make sure we can actually display for the current user
		if ($memberAccess == 'anyone'
			|| ($memberAccess == 'registered' && !JFactory::getUser()->get('guest'))
			|| ($memberAccess == 'members' && $isMember))
		{
			$html = $this->renderMembers($this->group, $members);
		}
		else
		{
			$html = '';
		}

		//return rendered events
		return $html;
	}

	/**
	 * Get a list of events for a group
	 *
	 * @param      object $group
	 * @param      array  $filters
	 * @return     array
	 */
	private function getGroupMembers($group, $filters = array())
	{
		// get members from group
		$members = $group->get('members');

		// get group params
		$params = \JComponentHelper::getParams("com_groups");
		$displaySystemUsers = $params->get('display_system_users', 'no');

		//get this groups params
		$gparams = new \JParameter($group->get('params'));
		$displaySystemUsers = $gparams->get('display_system_users', $displaySystemUsers);

		// filter is system users
		if ($displaySystemUsers == 'no')
		{
			$members = array_map(function($userid) {
				return ($userid < 1000) ? null : $userid;
			}, $members);
			$members = array_values(array_filter($members));
		}

		// shuffle order
		shuffle($members);

		// limit members based on the filter
		$members = array_slice($members, 0, $filters['limit']);

		//return members
		return $members;
	}

	/**
	 * Render the events
	 *
	 * @param      array     Array of group events
	 * @return     string
	 */
	private function renderMembers($group, $members)
	{
		$content = '<div class="member_browser">';
		if (count($members) > 0)
		{
			foreach ($members as $member)
			{
				$profile = \Hubzero\User\Profile::getInstance($member);
				$link    = \JRoute::_('index.php?option=com_members&id='.$profile->get('uidNumber'));

				$content .= '<a href="' . $link . '" class="member" title="Go to ' . stripslashes($profile->get('name')) . '\'s Profile.">';
				$content .= '<img src="' . $profile->getPicture(0, true) . '" alt="' . stripslashes($profile->get('name')) . '" class="member-border" width="50px" height="50px" />';
				$content .= '<span class="name">' . stripslashes($profile->get('name')) . '</span>';
				$content .= '<span class="org">' . stripslashes($profile->get('organization')) . '</span>';
				$content .= '</a>';
			}
		}
		$content .= '</div><!-- /.member_browser -->';

		return $content;
	}
}

