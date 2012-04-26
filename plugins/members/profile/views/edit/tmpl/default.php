<?php if($this->isUser) : ?>
	<div class="section-edit-container">
		<div class="section-edit-content">
			<form action="index.php" method="post" data-section-registation="<?php echo $this->registration_field; ?>" data-section-profile="<?php echo $this->profile_field; ?>">
				<span class="section-edit-errors"></span>
				<?php echo $this->inputs; ?>
				<?php echo $this->access; ?>

				<input type="submit" class="section-edit-submit" value="Save" /> 
				<input type="reset" class="section-edit-cancel" value="Cancel" />
				<input type="hidden" name="field_to_check[]" value="<?php echo $this->registration_field; ?>" /> 
				<input type="hidden" name="option" value="com_members" />
				<input type="hidden" name="id" value="<?php echo $this->profile->get("uidNumber"); ?>" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="no_html" value="1" />
			</form>
		</div>
	</div>
<?php endif;?>	 