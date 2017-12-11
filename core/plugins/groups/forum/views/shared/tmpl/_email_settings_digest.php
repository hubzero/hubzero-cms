<?php
	$base = $this->base;
?>

<p>
	<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CURRENT_SETTINGS'); ?>
	<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CURRENT_SETTINGS_' . $this->recvEmailOptionValue); ?>
	<br />
	<a href="#" class="edit-forum-options">
		<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CHANGE_SETTINGS'); ?>
	</a>
</p>
<div class="edit-forum-options-panel">
	<form method="post" action="<?php echo Route::url($base); ?>" id="forum-options-extended">
		<div>
			<input type="checkbox" class="edit-forum-options-receive-emails" value="1" name="recvpostemail"<?php if ($this->recvEmailOptionValue >= 1) { echo ' checked="checked"'; } ?> />
			<label>
				<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_TOGGLE'); ?>
			</label>
		</div>
		<div class="edit-forum-options-as">
			<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_INTERVAL'); ?>
		</div>
		<div>
			<input type="radio" name="recvpostemail" class="edit-forum-options-immediate" value="1"<?php if ($this->recvEmailOptionValue == 1) { echo ' checked="checked"'; } ?><?php if ($this->recvEmailOptionValue == 0) { echo ' disabled="disabled"'; } ?> />
			<label>
				<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_IMMEDIATELY'); ?>
			</label>
		</div>
		<div>
			<input type="radio" name="recvpostemail" class="edit-forum-options-digest" value="2"<?php if ($this->recvEmailOptionValue >= 2) { echo ' checked="checked"'; } ?><?php if ($this->recvEmailOptionValue == 0) { echo ' disabled="disabled"'; } ?> />
			<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_AS_A'); ?>
			<select name="recvpostemail" class="edit-forum-options-frequency"<?php if ($this->recvEmailOptionValue < 2) { echo ' disabled="disabled"'; } ?>>
				<option value="2"<?php if ($this->recvEmailOptionValue == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_DAILY'); ?></option>
				<option value="3"<?php if ($this->recvEmailOptionValue == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_WEEKLY'); ?></option>
				<option value="4"<?php if ($this->recvEmailOptionValue == 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_MONTHLY'); ?></option>
			</select>
			<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_DIGEST'); ?>
		</div>

		<input type="hidden" name="action" value="savememberoptions" />
		<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID; ?>" />
		<?php echo Html::input('token'); ?>

		<div class="edit-forum-options-actions">
			<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />
			<input type="button" class="btn edit-forum-options-cancel" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_CANCEL'); ?>" />
		</div>
	</form>
</div>

