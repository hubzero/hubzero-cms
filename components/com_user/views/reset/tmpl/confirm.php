<?php defined('_JEXEC') or die; ?>

<div id="content-header">
	<h2><?php echo JText::_('Confirm your Account'); ?></h2>
</div>

<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=confirmreset' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
	<fieldset>
		<legend><?php echo JText::_('Email New Password'); ?></legend>
			
		<p><?php echo JText::_('RESET_PASSWORD_CONFIRM_DESCRIPTION'); ?></p>
		<label for="username" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_USERNAME_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_USERNAME_TIP_TEXT'); ?>"><?php echo JText::_('User Name'); ?>:</label>
		<input id="username" name="username" type="text" class="required" size="36" />
		<label for="token" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TEXT'); ?>"><?php echo JText::_('Token'); ?>:</label>
		<input id="token" name="token" type="text" class="required" size="36" />
	</fieldset>
	<div class="clear"></div>
	
	<p class="submit"><button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button></p>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
