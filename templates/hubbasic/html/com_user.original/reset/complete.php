<?php defined('_JEXEC') or die; ?>

<div id="content-header">
	<h2><?php echo JText::_('Reset your Password'); ?></h2>
</div>

<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=completereset' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
	<fieldset>
		<legend><?php echo JText::_('New Password'); ?></legend>
		
		<p><?php echo JText::_('RESET_PASSWORD_COMPLETE_DESCRIPTION'); ?></p>

		<label for="password1" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_PASSWORD1_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_PASSWORD1_TIP_TEXT'); ?>"><?php echo JText::_('Password'); ?>:</label>
		<input id="password1" name="password1" type="password" class="required validate-password" />

		<label for="password2" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_PASSWORD2_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_PASSWORD2_TIP_TEXT'); ?>"><?php echo JText::_('Verify Password'); ?>:</label>
		<input id="password2" name="password2" type="password" class="required validate-password" />
	</fieldset>
	<div class="clear"></div>
	
	<p class="submit"><button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button></p>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>