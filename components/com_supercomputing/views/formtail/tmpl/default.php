	<h4>Submit request</h4>	
	<fieldset>
		<?php $this->errors_on('captcha'); ?>
		<div id="recap"<?php $this->error_class('captcha'); ?>>
			<?php if ($this->show_captcha()) echo Recaptcha::get_captcha(); ?>
		</div>
		<p>
			<?php echo JHTML::_('form.token'); ?>
			<input type="hidden" name="task" value="submit_<?php echo $this->request_type; ?>" />
			<input id="submit" type="submit" value="<?php echo $this->get_submit_label(); ?>" />
		</p>
	</fieldset>
</form>
