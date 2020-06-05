<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
			$areas['icon-class'] = 'icon-group';
			$areas['menu'] = $this->params->get('display_tab', 1);
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
			'metadata' => array()
		);

		$applicants = array();
		$invitees   = array();
		$members    = array();
		$managers   = array();

		// Look for any invites by email
		$emails = Hubzero\User\Group\InviteEmail::all()
			->whereEquals('email', $member->get('email'))
			->rows();

		foreach ($emails as $emailed)
		{
			$g = Hubzero\User\Group::getInstance($emailed->get('gidNumber'));

			if (!$g || !$g->get('gidNumber'))
			{
				continue;
			}

			$group = new stdClass;
			$group->gidNumber = $g->gidNumber;
			$group->published = $g->published;
			$group->approved = $g->approved;
			$group->cn = $g->cn;
			$group->description = $g->description;
			$group->logo = $g->logo;
			$group->created = $g->created;
			$group->join_policy = $g->join_policy;
			$group->registered = 0;
			$group->regconfirmed = 1;
			$group->manager = 0;

			$invitees[] = $group;
		}

		$groups = self::getGroups($member->get('id'), 'all', 1);

		if ($groups)
		{
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
			$state  = strtolower(Request::getWord('state', 'active', 'get'));

			if (in_array($state, array('active', 'archived')))
			{
				$g = array();

				switch ($state)
				{
					case 'archived':
						foreach ($groups as $key => $group)
						{
							if ($group->published == 2)
							{
								$g[$key] = $group;
							}
						}
					break;

					case 'active':
					default:
						foreach ($groups as $key => $group)
						{
							if ($group->published != 2)
							{
								$g[$key] = $group;
							}
						}
					break;
				}

				$groups = $g;
				$total = count($groups);
			}

			if (in_array($filter, array('invitees', 'applicants', 'members', 'managers')))
			{
				$g = array();

				switch ($filter)
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
				->set('state', $state)
				->set('groups', $groups)
				->set('member', $member)
				->set('option', 'com_groups')
				->setErrors($this->getErrors());

			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		if ($returnmeta)
		{
			// Display a message if it's me
			if ($member->get('id') == $user->get('id'))
			{
				$arr['metadata']['count'] = count($groups);

				if (count($invitees))
				{
					$title = Lang::txt('PLG_MEMBERS_GROUPS_NEW_INVITATIONS', count($invitees));
					$link = Route::url($member->link() . '&active=groups&filter=invitees');

					$arr['metadata']['alert'] = '<a class="alrt" href="' . $link . '"><span>' . $title . '</span></a>';

					Notify::info($arr['metadata']['alert'], 'com_members.profile');
				}
			}
		}

		return $arr;
	}

	/**
	 * Get a list of groups for a user
	 *
	 * @param   string  $uid
	 * @param   string  $type
	 * @param   string  $cat
	 * @return  boolean
	 */
	public static function getGroups($uid, $type='all', $cat = null)
	{
		$db = \App::get('db');

		$g = '';
		if ($cat == 1)
		{
			$g .= "(g.type=" . $db->quote($cat) . " OR g.type='3') AND";
		}
		elseif ($cat !== null)
		{
			$g .= "g.type=" . $db->quote($cat) . " AND ";
		}

		// Get all groups the user is a member of
		$query1 = "SELECT g.gidNumber, g.published, g.approved, g.cn, g.description, g.logo, g.created, g.join_policy, '1' AS registered, '0' AS regconfirmed, '0' AS manager FROM `#__xgroups` AS g, `#__xgroups_applicants` AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;
		$query2 = "SELECT g.gidNumber, g.published, g.approved, g.cn, g.description, g.logo, g.created, g.join_policy, '1' AS registered, '1' AS regconfirmed, '0' AS manager FROM `#__xgroups` AS g, `#__xgroups_members` AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;
		$query3 = "SELECT g.gidNumber, g.published, g.approved, g.cn, g.description, g.logo, g.created, g.join_policy, '1' AS registered, '1' AS regconfirmed, '1' AS manager FROM `#__xgroups` AS g, `#__xgroups_managers` AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;
		$query4 = "SELECT g.gidNumber, g.published, g.approved, g.cn, g.description, g.logo, g.created, g.join_policy, '0' AS registered, '1' AS regconfirmed, '0' AS manager FROM `#__xgroups` AS g, `#__xgroups_invitees` AS m WHERE $g m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;

		switch ($type)
		{
			case 'applicants':
				$query = $query1 . " ORDER BY description, cn";
				break;
			case 'members':
				$query = $query2 . " ORDER BY description, cn";
				break;
			case 'managers':
				$query = $query3 . " ORDER BY description, cn";
				break;
			case 'invitees':
				$query = $query4 . " ORDER BY description, cn";
				break;
			case 'all':
			default:
				$query = "( $query1 ) UNION ( $query2 ) UNION ( $query3 ) UNION ( $query4 )";
				break;
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (empty($result))
		{
			return false;
		}

		return $result;
	}
}
