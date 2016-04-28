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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for groups
 */
class plgMembersGroups extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @return  array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		// default areas returned to nothing
		$areas = array();

		// if this is the logged in user show them
		if ($user->get('id') == $member->get('id'))
		{
			$areas['groups'] = Lang::txt('PLG_MEMBERS_GROUPS');
			$areas['icon'] = 'f042';
		}

		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 *
	 * @param   object  $user    User
	 * @param   object  $member  Profile
	 * @param   string  $option  Component name
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
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
			'html'     => '',
			'metadata' => ''
		);

		$applicants = array();
		$invitees   = array();
		$members    = array();
		$managers   = array();

		$groups = Hubzero\User\Helper::getGroups($member->get('id'), 'all', 1);

		foreach ($groups as $item)
		{
			if ($item->registered)
			{
				if (!$item->regconfirmed)
				{
					$applicants[] = $item;
				}
				else
				{
					if ($item->manager)
					{
						$managers[] = $item;
					}
					else
					{
						$members[] = $item;
					}
				}
			}
			else
			{
				$invitees[] = $item;
			}
		}

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
		// SORT_NATURAL is PHP 5.4+
		if (PHP_VERSION_ID > 50400)
		{
			ksort($groups, SORT_NATURAL|SORT_FLAG_CASE);
		}
		else
		{
			ksort($groups);
		}

		// Build the final HTML
		if ($returnhtml)
		{
			$total  = count($groups);
			$filter = strtolower(Request::getWord('filter', '', 'get'));

			if (in_array($filter, array('invitees', 'applicants', 'members', 'managers')))
			{
				$g = array();

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

			$view = $this->view('default', 'summary')
				->set('total', $total)
				->set('filter', $filter)
				->set('groups', $groups)
				->set('member', $member)
				->set('option', 'com_groups')
				->setErrors($this->getErrors());

			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		if ($returnmeta)
		{
			//display a different message if its me
			if ($member->get('id') == $user->get('id'))
			{
				$arr['metadata']['count'] = count($groups);

				if (count($invitees))
				{
					$title = Lang::txt('PLG_MEMBERS_GROUPS_NEW_INVITATIONS', count($invitees));
					$link = Route::url($member->link() . '&active=groups&filter=invitees');
					$arr['metadata']['alert'] = '<a class="alrt" href="' . $link . '"><span><h5>' . Lang::txt('PLG_MEMBERS_GROUPS_ALERT') . '</h5>' . $title . '</span></a>';
				}
			}
		}

		return $arr;
	}
}
