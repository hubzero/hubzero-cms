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

// Check to ensure this file is included in Joomla!
defined('_HZEXEC_') or die( 'Restricted access' );

$group_statuses = array(
	'all' => Lang::txt('All Group Members'),
	'managers' => Lang::txt('All Group Managers'),
	'invitees' => Lang::txt('All Group Invitees'),
	'applicants' => Lang::txt('All Group Applicants')
);

$role_name = '';
$role_id = Request::getVar('role_id');
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
?>
<div class="subject">
	<?php if (!$this->no_html): ?>
	<ul class="entries-menu">
		<li><a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages'); ?>"><span><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SENT'); ?></span></a></li>
		<li><a class="active" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages&action=new'); ?>"><span><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SEND'); ?></span></a></li>
	</ul>
	<br class="clear" />
	<?php endif; ?>

	<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages'); ?>" method="post" id="hubForm<?php if ($this->no_html) { echo '-ajax'; }; ?>">
		<fieldset class="hub-mail">
			<div class="cont" style="background:#fff url('<?php echo $this->params->get('stamp_logo'); ?>') no-repeat 99% 4%;">
				<h3><?php echo Lang::txt('Compose Message to Group'); ?></h3>
				<label class="width-65"><?php echo Lang::txt('GROUP_MESSAGE_USERS'); ?>  <span class="required">Required</span>
					<select name="users[]" id="msg-recipient">
						<optgroup label="Group Status">
							<?php foreach ($group_statuses as $val => $name) { ?>
								<?php $sel = ($val == $this->users[0]) ? "selected" : ""; ?>
								<option <?php echo $sel; ?> value="<?php echo $val; ?>"><?php echo $name; ?></option>
							<?php } ?>
						</optgroup>
						<?php if (count($this->member_roles) > 0) { ?>
							<optgroup label="Group Member Roles">
								<?php foreach ($this->member_roles as $role) { ?>
									<?php $sel = ($role['name'] == $role_name) ? "selected" : ""; ?>
									<option <?php echo $sel; ?> value="role_<?php echo $role['id']; ?>"><?php echo $role['name']; ?></option>
								<?php } ?>
							</optgroup>
						<?php } ?>
						<?php if (count($this->members) > 0) { ?>
							<optgroup label="Group Members">
								<?php foreach ($this->members as $m) { ?>
									<?php $u = JUser::getInstance($m); ?>
									<?php $sel = ($u->get('id') == $this->users[0]) ? "selected" : ""; ?>
									<option <?php echo $sel; ?> value="<?php echo $u->get('id'); ?>"><?php echo $u->get('name'); ?></option>
								<?php } ?>
							</optgroup>
						<?php } ?>
					</select>
				</label>
				<label>
					<?php echo Lang::txt('GROUP_MESSAGE_SUBJECT'); ?> <span class="required">Required</span>
					<input type="text" name="subject" id="msg-subject" value="" />
				</label>
				<label>
					<?php echo Lang::txt('GROUP_MESSAGE'); ?> <span class="required">Required</span>
					<textarea name="message" id="msg-message" rows="12" cols="50"></textarea>
				</label>
				<p class="submit">
					<input type="submit" value="<?php echo Lang::txt('GROUP_MESSAGE_SEND'); ?>" />
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

