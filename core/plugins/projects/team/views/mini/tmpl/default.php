<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$show    = array();
$sorting = array();
$online  = array();

// get all sessions
$sessions = Hubzero\Session\Helper::getAllSessions(array(
	'guest'    => 0,
	'distinct' => 1
));

// Determine if there is anything worth showing
foreach ($this->team as $member)
{
	$member->online = 0;

	// Member online?
	if ($sessions)
	{
		// see if any session matches our userid
		foreach ($sessions as $session)
		{
			if ($session->userid == $member->userid)
			{
				$member->online = 1;
			}
		}
	}

	if (($member->lastvisit || !empty($member->online)))
	{
		// Recent visits?
		if ($member->online)
		{
			$member->lastvisit = Date::of('now')->toSql();
		}
		$show[$member->lastvisit] = $member;
	}
}

// Does not make sense to show only current member
if (count($show) < 2)
{
	return;
}

krsort($show);

$i = 0;
?>
<div class="sidebox">
		<h4><a href="<?php echo Route::url($this->model->link('team')); ?>" class="hlink"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_RECENT_VISITS')); ?></a></h4>
	<ul>
	<?php foreach ($show as $owner)
	{
		// Do not show more than 5
		if ($i >= 5)
		{
			break;
		}
		// Get profile thumb image
		$profile = User::getInstance($owner->userid);
		$actor   = User::getInstance(User::get('id'));
		$thumb   = $profile->get('id') ? $profile->picture() : $actor->picture(true);

		$timecheck = date('Y-m-d H:i:s', time() - (15 * 60));
		$lastvisit = $owner->lastvisit && $owner->lastvisit != '0000-00-00 00:00:00'
					? \Components\Projects\Helpers\Html::timeAgo($owner->lastvisit) . ' ' . Lang::txt('PLG_PROJECTS_TEAM_AGO')
					: Lang::txt('PLG_PROJECTS_TEAM_NEVER');
		$lastvisit = $owner->userid == User::get('id') || (!empty($owner->online) && $owner->lastvisit > $timecheck)
					? '<span class="now-online">' . Lang::txt('PLG_PROJECTS_TEAM_ONLINE_NOW') . '</span>'
					: $lastvisit;
		$i++;
	?>
		<li>
			<span class="pub-thumb"><img src="<?php echo $thumb; ?>" alt=""/></span>
			<span class="pub-details">
				<span class="block"><a href="/members/<?php echo $owner->userid; ?>"><?php echo $owner->fullname; ?></a></span>
				<span class="block"><?php echo $lastvisit; ?></span>
			</span>
		</li>
	<?php } ?>
	</ul>
</div>