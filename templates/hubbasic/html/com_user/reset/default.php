<?php defined('_JEXEC') or die; ?>

<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div id="content-header">
		<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
	</div>
<?php endif; ?>

	<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=requestreset' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">
		<fieldset>
			<h3><?php echo JText::_('Email Verification Token'); ?></h3>
						
			<p><?php echo JText::_('RESET_PASSWORD_REQUEST_DESCRIPTION'); ?></p>			
			<label for="username" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_USERNAME_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_USERNAME_TIP_TEXT'); ?>"><?php echo JText::_('User Name'); ?>:</label>
			<input id="username" name="username" type="text" class="required" size="36" />
	
		</fieldset>
		<div class="clear"></div>
			
		<p class="submit"><button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button></p>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
