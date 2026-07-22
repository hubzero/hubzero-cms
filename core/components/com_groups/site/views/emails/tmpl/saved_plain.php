<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$gparams = new \Hubzero\Config\Registry($this->group->get('params'));

$base = rtrim(str_replace('administrator', '', Request::base()), '/');
$sef  = ltrim(Route::url('index.php?option='.$this->option.'&cn='. $this->group->get('cn')), '/');

// Pre-change values (keyed) for noting what changed. Empty for a new group,
// in which case no change markers are shown.
$before = (isset($this->before) && is_array($this->before)) ? $this->before : array();

// Return a "*** was: <old> ***" marker when a field's display value changed.
$wasText = function($key, $newDisplay, $oldDisplay) use ($before)
{
	if (!array_key_exists($key, $before))
	{
		return '';
	}
	if ((string) $newDisplay === (string) $oldDisplay)
	{
		return '';
	}
	$old = ($oldDisplay === '' || $oldDisplay === null) ? '[ Empty ]' : $oldDisplay;
	return '   *** ' . Lang::txt('COM_GROUPS_EMAIL_WAS', $old) . ' ***';
};

// Shared value->label formatters so the old and new sides render identically
$joinPolicyLabel = function($v)
{
	switch ($v)
	{
		case 3:  return Lang::txt('Closed');
		case 2:  return Lang::txt('Invite Only');
		case 1:  return Lang::txt('Restricted');
		default: return Lang::txt('Open');
	}
};
$discoverabilityLabel = function($v)
{
	return ($v == 1) ? Lang::txt('Hidden') : Lang::txt('Visible');
};
$pageCommentsLabel = function($v)
{
	if ($v == 2)
	{
		return Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK');
	}
	if ($v == 1)
	{
		return Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES');
	}
	return Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO');
};
$pageAuthorLabel = function($v)
{
	return ($v == 1)
		? Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_YES')
		: Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_NO');
};
$onOffLabel = function($v)
{
	return $v ? Lang::txt('On') : Lang::txt('Off');
};

$message  = Lang::txt('Group Updated') . "\n\n";
$message .= "--------------------------------------------\n";
$message .= Lang::txt('Group:') . ' ' . strip_tags($this->group->get('description')) . "\n";
$message .= Lang::txt('Alias:') . ' ' . $this->group->get('cn') . "\n";
$message .= Lang::txt('Updated:') . ' @ ' . Date::of('now')->toLocal(Lang::txt('TIME_FORMAT_HZ1')) . ' on ' . Date::of('now')->toLocal(Lang::txt('DATE_FORMAT_HZ1')) . "\n";
$message .= Lang::txt('Updated By:') . ' ' . $this->user->get('name') . ' ('.$this->user->get('email').')' . "\n";
$message .= "--------------------------------------------\n\n";

$nameNew = strip_tags($this->group->get('description'));
$message .= "\t" . Lang::txt('Name:') . ' ' . $nameNew;
$message .= $wasText('description', $nameNew, isset($before['description']) ? strip_tags($before['description']) : null) . "\n\n";

$message .= "\t" . Lang::txt('Interests (Tags):') . ' ';

	$gt = new \Components\Groups\Models\Tags($this->group->get('gidNumber'));
	$tags = $gt->render('string');

	$message .= ($tags ? $tags : Lang::txt('[ Empty ]'));
	$message .= $wasText('tags', $tags, isset($before['tags']) ? $before['tags'] : null) . "\n\n";

$publicNew = strip_tags($this->group->get('public_desc', '[ Empty ]'));
$message .= "\t" . Lang::txt('Public Description:') . ' ' . $publicNew;
$message .= $wasText('public_desc', $this->group->get('public_desc'), isset($before['public_desc']) ? strip_tags($before['public_desc']) : null) . "\n\n";

$privateNew = strip_tags($this->group->get('private_desc', '[ Empty ]'));
$message .= "\t" . Lang::txt('Private Description:') . ' ' . $privateNew;
$message .= $wasText('private_desc', $this->group->get('private_desc'), isset($before['private_desc']) ? strip_tags($before['private_desc']) : null) . "\n\n";

$message .= "\t" . Lang::txt('Logo:') . ' ' . ($this->group->get('logo') ? $base . DS . ltrim($this->group->getLogo(), DS) : '[ Not set ]');
$message .= $wasText('logo', $this->group->get('logo'), isset($before['logo']) ? ($before['logo'] ? $before['logo'] : '[ Not set ]') : null) . "\n\n";

$message .= "\t" . Lang::txt('Membership Settings/Join Policy:') . ' ';
	$policy = $joinPolicyLabel($this->group->get('join_policy'));
	$message .= $policy;
	$message .= $wasText('join_policy', $policy, isset($before['join_policy']) ? $joinPolicyLabel($before['join_policy']) : null) . "\n\n";

$message .= "\t" . Lang::txt('Discoverability:') . ' ';
	$discoverability = $discoverabilityLabel($this->group->get('discoverability'));
	$message .= $discoverability;
	$message .= $wasText('discoverability', $discoverability, isset($before['discoverability']) ? $discoverabilityLabel($before['discoverability']) : null) . "\n\n";

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
	$oldAccess = (isset($before['access']) && is_array($before['access'])) ? $before['access'] : null;

	foreach ($group_plugins as $plugin)
	{
		if ($plugin['display_menu_tab'] == 1)
		{
			$title  = $plugin['title'];
			$perm = $access[$plugin['name']];
			$line = "\t\t" .$title . ' => ' . $levels[$perm];
			if ($oldAccess !== null && isset($oldAccess[$plugin['name']]) && $oldAccess[$plugin['name']] != $perm)
			{
				$oldPerm  = $oldAccess[$plugin['name']];
				$oldLabel = isset($levels[$oldPerm]) ? $levels[$oldPerm] : $oldPerm;
				$line .= '   *** ' . Lang::txt('COM_GROUPS_EMAIL_WAS', $oldLabel) . ' ***';
			}
			$message .= $line . "\n";
		}
	}
	$message .= "\n";

$params = Component::params('com_groups');
if ($params->get('email_forum_comments'))
{
	$autosub = $onOffLabel($this->group->get('discussion_email_autosubscribe'));
	$message .= "\t" . Lang::txt('Discussion Group Emails Autosubscribe:') . ' ' . $autosub;
	$message .= $wasText('discussion_email_autosubscribe', $autosub, isset($before['discussion_email_autosubscribe']) ? $onOffLabel($before['discussion_email_autosubscribe']) : null) . "\n\n";
}

$message .= "\t" . Lang::txt('Page Comments:') . ' ';
	$pageComments = $pageCommentsLabel($gparams->get('page_comments'));
	$message .= $pageComments;
	$message .= $wasText('page_comments', $pageComments, isset($before['page_comments']) ? $pageCommentsLabel($before['page_comments']) : null) . "\n";

$message .= "\t" . Lang::txt('Page Author Details:') . ' ';
	$pageAuthor = $pageAuthorLabel($gparams->get('page_author'));
	$message .= $pageAuthor;
	$message .= $wasText('page_author', $pageAuthor, isset($before['page_author']) ? $pageAuthorLabel($before['page_author']) : null) . "\n";

$message .= "\n\n";
$message .= $base . '/' . $sef."\n";

echo $message;
