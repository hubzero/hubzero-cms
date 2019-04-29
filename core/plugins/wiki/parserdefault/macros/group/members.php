<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Macros\Group;

require_once dirname(__DIR__) . DS . 'group.php';

use Plugins\Wiki\Parserdefault\Macros\GroupMacro;

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
			return \Lang::txt('[This macro is designed for Groups only]');
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
		$isMember = (in_array(\User::get('id'), $this->group->get('members'))) ? true : false;

		//get the members plugin access for this group
		$memberAccess = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'members');

		// make sure we can actually display for the current user
		if ($memberAccess == 'anyone'
			|| ($memberAccess == 'registered' && !\User::isGuest())
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
		$params = \Component::params("com_groups");
		$displaySystemUsers = $params->get('display_system_users', 'no');

		//get this groups params
		$gparams = new \Hubzero\Config\Registry($group->get('params'));
		$displaySystemUsers = $gparams->get('display_system_users', $displaySystemUsers);

		// filter is system users
		if ($displaySystemUsers == 'no')
		{
			$members = array_map(
				function($userid)
				{
					return ($userid < 1000) ? null : $userid;
				},
				$members
			);
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
	 * @param      array  $group   Array of group events
	 * @param      array  $members Array of members
	 * @return     string
	 */
	private function renderMembers($group, $members)
	{
		$content = '<div class="member_browser">';
		if (count($members) > 0)
		{
			include_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

			foreach ($members as $member)
			{
				$profile = \Components\Members\Models\Member::oneOrNew($member);
				$link    = \Route::url($profile->link());

				$content .= '<a href="' . $link . '" class="member" title="Go to ' . stripslashes($profile->get('name')) . '\'s Profile.">';
				$content .= '<img src="' . $profile->picture(0, true) . '" alt="' . stripslashes($profile->get('name')) . '" class="member-border" width="50px" height="50px" />';
				$content .= '<span class="name">' . stripslashes($profile->get('name')) . '</span>';
				$content .= '<span class="org">' . stripslashes($profile->get('organization')) . '</span>';
				$content .= '</a>';
			}
		}
		$content .= '</div><!-- /.member_browser -->';

		return $content;
	}
}

