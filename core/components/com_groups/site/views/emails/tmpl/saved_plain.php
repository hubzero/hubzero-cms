<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$gparams = new \Hubzero\Config\Registry($this->group->get('params'));

$base = rtrim(str_replace('administrator', '', Request::base()), '/');
$sef  = ltrim(Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn')), '/');

$message  = Lang::txt('Group Updated') . "\n\n";
$message .= "--------------------------------------------\n";
$message .= Lang::txt('Group:') . ' ' . strip_tags($this->group->get('description')) . "\n";
$message .= Lang::txt('Alias:') . ' ' . $this->group->get('cn') . "\n";
$message .= Lang::txt('Updated:') . ' @ ' . Date::of('now')->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . ' on ' . Date::of('now')->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . "\n";
$message .= Lang::txt('Updated By:') . ' ' . $this->user->get('name') . ' ('.$this->user->get('email').')' . "\n";
$message .= "--------------------------------------------\n\n";

$message .= "\t" . Lang::txt('Name:') . ' ' . strip_tags($this->group->get('description')) . "\n\n";
$message .= "\t" . Lang::txt('Interests (Tags):') . ' ';

	$gt = new \Components\Groups\Models\Tags($this->group->get('gidNumber'));
	$tags = $gt->render('string');

	$message .= ($tags ? $tags : Lang::txt('[ Empty ]')) . "\n\n";

$message .= "\t" . Lang::txt('Public Description:') . ' ' . strip_tags($this->group->get('public_desc', '[ Empty ]')) . "\n\n";
$message .= "\t" . Lang::txt('Private Description:') . ' ' . strip_tags($this->group->get('private_desc', '[ Empty ]')) . "\n\n";
$message .= "\t" . Lang::txt('Logo:') . ' ' . ($this->group->get('logo') ? $base . DS . ltrim($this->group->getLogo(), DS) : '[ Not set ]') . "\n\n";
$message .= "\t" . Lang::txt('Membership Settings/Join Policy:') . ' ';
	switch ($this->group->get('join_policy'))
	{
		case 3: $policy = Lang::txt('Closed');      break;
		case 2: $policy = Lang::txt('Invite Only'); break;
		case 1: $policy = Lang::txt('Restricted');  break;
		case 0:
		default: $policy = Lang::txt('Open'); break;
	}
	$message .= $policy . "\n\n";

$message .= "\t" . Lang::txt('Discoverability:') . ' ';
	switch ($this->group->get('discoverability'))
	{
		case 1:  $discoverability = Lang::txt('Hidden'); break;
		case 0:
		default: $discoverability = Lang::txt('Visible'); break;
	}
	$message .= $discoverability . "\n\n";

$message .= "\t" . Lang::txt('Access Permissions:') . "\n";
	$levels = array(
		'anyone' => 'Any HUB Visitor',
		'registered' => 'Only Registered User of the HUB',
		'members' => 'Only Group Members',
		'nobody' => 'Disabled/Off'
	);

	// Get plugins
	$group_plugins = Event::trigger('groups.onGroupAreas', array());
	array_unshift($group_plugins, array(
		'name'             => 'overview',
		'title'            => 'Overview',
		'default_access'   => 'anyone',
		'display_menu_tab' => true
	));

	$access = \Hubzero\User\Group\Helper::getPluginAccess($this->group);

	foreach ($group_plugins as $plugin)
	{
		if ($plugin['display_menu_tab'] == 1)
		{
			$title  = $plugin['title'];
			$perm = $access[$plugin['name']];
			$message .= "\t\t" .$title . ' => ' . $levels[$perm] . "\n";
		}
	}
	$message .= "\n";

$params = Component::params('com_groups');
if ($params->get('email_comment_processing'))
{
	$message .= "\t" . Lang::txt('Discussion Group Emails Autosubscribe:') . ' ' . ($this->group->get('discussion_email_autosubscribe') ? Lang::txt('On') : Lang::txt('Off')) . "\n\n";
}

$message .= "\t" . Lang::txt('Page Comments:') . ' ';
	if ($gparams->get('page_comments') == 2)
	{
		$message .= Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK');
	}
	elseif ($gparams->get('page_comments') == 1)
	{
		$message .= Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES');
	}
	else
	{
		$message .= Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO');
	}
	$message .= "\n";

$message .= "\t" . Lang::txt('Page Author Details:') . ' ';
	if ($gparams->get('page_author') == 1)
	{
		$message .= Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_YES');
	}
	else
	{
		$message .= Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_NO');
	}
	$message .= "\n";

$message .= "\n\n";
$message .= $base . '/' . $sef."\n";

echo $message;
