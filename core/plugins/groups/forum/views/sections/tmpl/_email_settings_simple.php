<?php
	$base = $this->base;
?>

<form method="post" action="<?php echo Route::url($base); ?>" id="forum-options">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_SETTINGS'); ?></legend>

		<input type="hidden" name="action" value="savememberoptions" />
		<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID; ?>" />
		<input type="hidden" name="postsaveredirect" value="<?php echo Route::url($base); ?>" />
		<?php echo Html::input('token'); ?>

		<label class="option" for="recvpostemail">
			<input type="checkbox" class="option" id="recvpostemail" value="1" name="recvpostemail"<?php if ($this->recvEmailOptionValue == 1) { echo ' checked="checked"'; } ?> />
			<?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS'); ?>
		</label>
		<input class="option" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />
	</fieldset>
</form>
