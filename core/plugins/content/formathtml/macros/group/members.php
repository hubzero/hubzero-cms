<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros\Group;

require_once dirname(__DIR__) . DS . 'group.php';

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
		// Check if we can render
		if (!parent::canRender())
		{
			return \Lang::txt('[This macro is designed for Groups only]');
		}

		// Get args
		$args = $this->getArgs();

		// Array of filters
		$filters = array(
			'limit' => (count($args) == 1 && is_numeric($args[0])) ? $args[0] : 12
		);

		// Get members
		$members = $this->getGroupMembers($this->group, $filters);

		// Are we a group member
		$isMember = (in_array(\User::get('id'), $this->group->get('members'))) ? true : false;

		// Get the members plugin access for this group
		$memberAccess = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'members');

		// Make sure we can actually display for the current user
		if ($memberAccess == 'anyone'
			|| ($memberAccess == 'registered' && !User::isGuest())
			|| ($memberAccess == 'members' && $isMember))
		{
			$html = $this->renderMembers($this->group, $members);
		}
		else
		{
			$html = '';
		}

		// Return rendered events
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
		// Get members from group
		$members = $group->get('members');

		// Get group params
		$params = \Component::params("com_groups");
		$displaySystemUsers = $params->get('display_system_users', 'no');

		// Get this groups params
		$gparams = new \Hubzero\Config\Registry($group->get('params'));
		$displaySystemUsers = $gparams->get('display_system_users', $displaySystemUsers);

		// Filter is system users
		if ($displaySystemUsers == 'no')
		{
			$members = array_map(function($userid) {
				return ($userid < 1000) ? null : $userid;
			}, $members);
			$members = array_values(array_filter($members));
		}

		// Shuffle order
		shuffle($members);

		// Limit members based on the filter
		$members = array_slice($members, 0, $filters['limit']);

		// Return members
		return $members;
	}

	/**
	 * Render the events
	 *
	 * @param   array  Array of group events
	 * @param   array  $members  Array of members
	 * @return  string
	 */
	private function renderMembers($group, $members)
	{
		$content = '<div class="member_browser">';
		if (count($members) > 0)
		{
			$profiles = \Hubzero\User\User::all()
				->whereIn('id', $members)
				->rows();

			foreach ($profiles as $profile)
			{
				$content .= '<a href="' . Route::url($profile->link()) . '" class="member" title="Go to ' . stripslashes($profile->get('name')) . '\'s Profile.">';
				$content .= '<img src="' . $profile->picture() . '" alt="' . stripslashes($profile->get('name')) . '" class="member-border" width="50px" height="50px" />';
				$content .= '<span class="name">' . stripslashes($profile->get('name')) . '</span>';
				$content .= '<span class="org">' . stripslashes($profile->get('organization')) . '</span>';
				$content .= '</a>';
			}
		}
		$content .= '</div><!-- /.member_browser -->';

		return $content;
	}
}

