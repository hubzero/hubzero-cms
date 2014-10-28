<?php if ($this->isUser) : ?>
	<div class="section-edit-container">
		<?php if ($this->registration == REG_READONLY) : ?>
			<p class="notice warning"><?php echo JText::sprintf('PLG_MEMBERS_PROFILE_READONLY', $this->title); ?></p>
		<?php else : ?>
			<div class="section-edit-content">
				<form action="index.php" method="post" data-section-registation="<?php echo $this->registration_field; ?>" data-section-profile="<?php echo $this->profile_field; ?>">
					<span class="section-edit-errors"></span>

					<?php echo $this->inputs; ?>
					<?php echo $this->access; ?>

					<input type="submit" class="section-edit-submit" value="<?php echo JText::_('PLG_MEMBERS_PROFILE_SAVE'); ?>" />
					<input type="reset" class="section-edit-cancel" value="<?php echo JText::_('PLG_MEMBERS_PROFILE_CANCEL'); ?>" />
					<input type="hidden" name="field_to_check[]" value="<?php echo $this->registration_field; ?>" />
					<input type="hidden" name="option" value="com_members" />
					<input type="hidden" name="id" value="<?php echo $this->profile->get("uidNumber"); ?>" />
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="no_html" value="1" />
				</form>
			</div>
		<?php endif; ?>
	</div>
<?php endif;?>