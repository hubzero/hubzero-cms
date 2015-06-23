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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-browse btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=members'); ?>">
			<?php echo Lang::txt('PLG_GROUPS_MEMBERS'); ?>
		</a>
	</li>
</ul>

<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=members'); ?>" method="post" id="hubForm" class="full">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_DETAILS'); ?></legend>
		<label>
			<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_NAME'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
			<input type="text" name="role[name]" value="<?php echo $this->role->name; ?>" >
		</label>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_ROLE_PERMISSIONS'); ?></legend>
			<?php foreach ($this->available_permissions as $perm => $label) : ?>
				<label>
					<?php $ckd = ($this->role->hasPermission($perm)) ? 'checked="checked"' : '' ?>
					<input type="hidden" name="role[permissions][<?php echo $perm; ?>]" value="0" />
					<input class="option" type="checkbox" <?php echo $ckd; ?> name="role[permissions][<?php echo $perm; ?>]" value="1"> <?php echo $label; ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
	</fieldset>
	<div class="clear"></div>

	<input type="hidden" name="role[id]" value="<?php echo $this->role->id; ?>" >
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="action" value="saverole" />

	<p class="submit">
		<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_SUBMIT'); ?>" />
	</p>
</form>
