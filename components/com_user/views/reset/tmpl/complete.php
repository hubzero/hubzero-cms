<?php defined('_JEXEC') or die; ?>

<div id="content-header">
	<h2><?php echo JText::_('Reset your Password'); ?></h2>
</div>

<div class="main section">
	<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=completereset' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
		<fieldset>
			<legend><?php echo JText::_('New Password'); ?></legend>

			<p><?php echo JText::_('RESET_PASSWORD_COMPLETE_DESCRIPTION'); ?></p>

			<label for="password1" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_PASSWORD1_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_PASSWORD1_TIP_TEXT'); ?>"><?php echo JText::_('Password'); ?>:</label>
			<input id="password1" name="password1" type="password" class="required validate-password" />

			<label for="password2" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_PASSWORD2_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_PASSWORD2_TIP_TEXT'); ?>"><?php echo JText::_('Verify Password'); ?>:</label>
			<input id="password2" name="password2" type="password" class="required validate-password" />

<?php
			// Add password rules if they apply
			if (count($this->password_rules) > 0) {
				echo "\t\t<ul id=\"passrules\">\n";
				foreach ($this->password_rules as $rule) {
					if (!empty($rule)) {
						if (is_array($this->validated)) {
							$err = in_array($rule, $this->validated);
						} else {
							$err = '';
						}

						$mclass = ($err)  ? ' class="error"' : '';
						echo "\t\t\t<li $mclass>".$rule."</li>\n";
					}
				}
				if (is_array($this->validated)) {
					foreach ($this->validated as $msg) {
						if (!in_array($msg,$this->password_rules)) {
							echo "\t\t\t".'<li class="error">'.$msg."</li>\n";
						}
					}
				}
				echo "\t\t\t</ul>\n";
			}
?>

		</fieldset>
		<div class="clear"></div>

		<input type="hidden" id="pass_no_html" name="no_html" value="0" />
		<input type="hidden" name="change" value="1" />
		<p class="submit"><button type="submit" id="password-change-save" class="validate"><?php echo JText::_('Submit'); ?></button></p>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div><!-- / .main section -->