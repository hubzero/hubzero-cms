<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<ul class="team-listing cf <?php echo (isset($this->cls)) ? $this->cls : ''; ?>">
	<?php foreach ($this->members as $member) : ?>
		<?php 
			$profile = $member->getProfile();
			$me      = ($profile->get('uidNumber') == User::get('id')) ? true : false;
		?>
		<li <?php echo ($me) ? 'class="me"' : ''; ?>>
			<a href="<?php echo $profile->getLink(); ?>" class="tooltips" title="<?php echo $profile->get('name'); ?> <?php echo ($me) ? '(You)' : ''; ?>">
				<img src="<?php echo $profile->getPicture(0, true); ?>" alt="" />
				<span><?php echo $profile->get('name'); ?></span>
			</a>
			<?php if (!$me) : ?>
				<a class="btn btn-danger btn-secondary remove confirm" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE_CONFIRM'); ?>" href="<?php echo Route::url($member->link('remove')); ?>">
					<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBER_REMOVE'); ?>
				</a>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>