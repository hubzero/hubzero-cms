<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
