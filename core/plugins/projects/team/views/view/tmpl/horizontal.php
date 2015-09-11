<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

?>
<div class="public-list-header">
	<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_TEAM'); ?></h3>
</div>
<div id="team-horiz" class="public-list-wrap">
	<?php
	if (count($this->team) > 0) { 	?>
		<ul>
			<?php foreach ($this->team as $owner)
			{
				if (!$owner->userid || $owner->status != 1)
				{
					continue;
				}
				// Get profile thumb image
				$profile = \Hubzero\User\Profile::getInstance($owner->userid);
				$actor   = \Hubzero\User\Profile::getInstance(User::get('id'));
				$thumb   = $profile ? $profile->getPicture() : $actor->getPicture(true);
			?>
			<li>
				<img width="50" height="50" src="<?php echo $thumb; ?>" alt="<?php echo $owner->fullname; ?>" />
				<span class="block"><a href="/members/<?php echo $owner->userid; ?>"><?php echo $owner->fullname; ?></a></span>
			</li>
			<?php }	?>
			<li class="clear">&nbsp;</li>
		</ul>
	<?php } else { ?>
		<div class="noresults"><?php echo Lang::txt('PLG_PROJECTS_TEAM_EXTERNAL_NO_TEAM'); ?></div>
	<?php } ?>
	<div class="clear"></div>
</div>
