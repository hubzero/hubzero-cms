<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
		$show[]    = $member;
		$sorting[] = $member->lastvisit;
		$online[]  = empty($member->online) ? 1 : 2;
	}
}

// Does not make sense to show only current member
if (count($show) < 2)
{
	return;
}

array_multisort($online, SORT_DESC, $show);
array_multisort($sorting, SORT_DESC, $show);

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
		$profile = \Hubzero\User\Profile::getInstance($owner->userid);
		$actor   = \Hubzero\User\Profile::getInstance(User::get('id'));
		$thumb   = $profile ? $profile->getPicture() : $actor->getPicture(true);

		$timecheck = date('Y-m-d H:i:s', time() - (15 * 60));
		$lastvisit = $owner->lastvisit && $owner->lastvisit != '0000-00-00 00:00:00'
					? \Components\Projects\Helpers\Html::timeAgo($owner->lastvisit) . ' ' . Lang::txt('PLG_PROJECTS_TEAM_AGO')
					: Lang::txt('PLG_PROJECTS_TEAM_NEVER');
		$lastvisit = $owner->userid == User::get('id') || (!empty($owner->online) && $owner->lastvisit > $timecheck)
					? '<span class="online">' . Lang::txt('PLG_PROJECTS_TEAM_TEAM_ONLINE_NOW') . '</span>'
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