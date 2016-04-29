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
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=members'); ?>" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_ASSIGN_ROLE'); ?></legend>

		<label for="uid">
			<input type="hidden" name="uid" value="<?php echo $this->escape($this->uid); ?>" id="uid" />
			<?php
				$u = User::getInstance($this->uid);

				$current_roles = array();
				$roles = Components\Groups\Helpers\Permissions::getGroupMemberRoles($u->get('id'), $this->group->get('gidNumber'));
				if ($roles)
				{
					foreach ($roles as $role)
					{
						$current_roles[] = $role['name'];
					}
				}
			?>
			<strong><?php echo Lang::txt('PLG_GROUPS_MEMBERS_MEMBER'); ?>: </strong> <?php echo $this->escape($u->get('name')); ?>
		</label>

		<label for="roles">
			<strong><?php echo Lang::txt('PLG_GROUPS_MEMBERS_SELECT_ROLE'); ?></strong>
			<select name="role" id="roles">
				<option value=""><?php echo Lang::txt('PLG_GROUPS_MEMBERS_OPT_SELECT_ROLE'); ?></option>
				<?php foreach ($this->roles as $role) { ?>
					<?php if (!in_array($role['name'],$current_roles)) { ?>
						<option value="<?php echo $role['id']; ?>"><?php echo $this->escape($role['name']); ?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</label>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="members" />
	<input type="hidden" name="action" value="submitrole" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

	<p class="submit">
		<input type="submit" name="submit" value="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_ASSIGN_ROLE'); ?>" />
	</p>
</form>
