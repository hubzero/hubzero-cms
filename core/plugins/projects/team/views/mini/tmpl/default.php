<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
					? '<span class="online">' . Lang::txt('PLG_PROJECTS_TEAM_ONLINE_NOW') . '</span>'
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