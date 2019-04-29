<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$group_statuses = array(
	'all' => Lang::txt('All Group Members'),
	'managers' => Lang::txt('All Group Managers'),
	'invitees' => Lang::txt('All Group Invitees'),
	'applicants' => Lang::txt('All Group Applicants')
);

$role_name = '';
$role_id = Request::getInt('role_id');
if ($role_id)
{
	foreach ($this->member_roles as $role)
	{
		if ($role['id'] == $role_id)
		{
			$role_name = $role['name'];
			break;
		}
	}
}

if ($this->params->get('stamp_logo'))
{
	$this->css('
		.hub-mail .cont {
			background: #fff url("' . $this->params->get('stamp_logo') . '") no-repeat 99% 4%;
		}
	');
}
?>
<div class="subject">
	<?php if (!$this->no_html): ?>
	<ul class="entries-menu">
		<li><a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages'); ?>"><span><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SENT'); ?></span></a></li>
		<li><a class="active" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages&action=new'); ?>"><span><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SEND'); ?></span></a></li>
	</ul>
	<br class="clear" />
	<?php endif; ?>

	<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages'); ?>" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax';
}; ?>">
		<fieldset class="hub-mail">
			<div class="cont">
				<h3><?php echo Lang::txt('Compose Message to Group'); ?></h3>
				<div class="form-group">
					<label class="width-65" for="msg-recipient">
						<?php echo Lang::txt('GROUP_MESSAGE_USERS'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
						<select name="users[]" id="msg-recipient" class="form-control">
							<optgroup label="Group Status">
								<?php foreach ($group_statuses as $val => $name) { ?>
									<?php $sel = ($val == $this->users[0]) ? 'selected="selected"' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $val; ?>"><?php echo $name; ?></option>
								<?php } ?>
							</optgroup>
							<?php if (count($this->member_roles) > 0) { ?>
								<optgroup label="Group Member Roles">
									<?php foreach ($this->member_roles as $role) { ?>
										<?php $sel = ($role['name'] == $role_name) ? 'selected="selected"' : ''; ?>
										<option <?php echo $sel; ?> value="role_<?php echo $role['id']; ?>"><?php echo $role['name']; ?></option>
									<?php } ?>
								</optgroup>
							<?php } ?>
							<?php if (count($this->members) > 0) { ?>
								<optgroup label="Group Members">
									<?php foreach ($this->members as $m) { ?>
										<?php $u = User::getInstance($m); ?>
										<?php $sel = ($u->get('id') == $this->users[0]) ? 'selected="selected"' : ''; ?>
										<option <?php echo $sel; ?> value="<?php echo $u->get('id'); ?>"><?php echo $u->get('name'); ?></option>
									<?php } ?>
								</optgroup>
							<?php } ?>
						</select>
					</label>
				</div>
				<div class="form-group">
					<label for="msg-subject">
						<?php echo Lang::txt('GROUP_MESSAGE_SUBJECT'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
						<input type="text" class="form-control" name="subject" id="msg-subject" value="" />
					</label>
				</div>
				<div class="form-group">
					<label for="msg-message">
						<?php echo Lang::txt('GROUP_MESSAGE'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
						<textarea class="form-control" name="message" id="msg-message" rows="12" cols="50"></textarea>
					</label>
				</div>
				<p class="submit">
					<input type="submit" class="btn" value="<?php echo Lang::txt('GROUP_MESSAGE_SEND'); ?>" />
				</p>
			<div><!-- /.cont -->
		</fieldset>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="active" value="messages" />
		<input type="hidden" name="action" value="send" />
		<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
	</form>
</div><!-- // .subject -->
