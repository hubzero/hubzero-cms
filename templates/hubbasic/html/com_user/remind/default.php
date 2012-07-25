<?php defined('_JEXEC') or die; ?>

<?php if($this->params->get('show_page_title',1)) : ?>
<div id="content-header">
	<h2><?php echo $this->escape($this->params->get('page_title')) ?></h2>
</div>
<?php endif; ?>

<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=remindusername' ); ?>" method="post" class="josForm form-validate" name="hubForm" id="hubForm">

	<div class="explaination">
		<p class="info">
			If you already know your username, and only need your password reset, <a href="<?php echo JRoute::_('/login/reset'); ?>">go here now</a>.
		</p>
	</div>
	<fieldset>
		<legend>Recover Username(s)</legend>

		<label for="email" class="hasTip" title="<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TEXT'); ?>"><?php echo JText::_('Email Address'); ?>:</label>
		<input id="email" name="email" type="text" size="36" class="required validate-email" />
			
		<p><?php echo JText::_('REMIND_USERNAME_DESCRIPTION'); ?></p>

		<div class="help">
		<h4>What if I have also lost my password?</h4>
		<p>
			Fill out this form to retrieve your username(s). The email you 
			receive will contain instructions on how to reset your password as well.
		</p>
			
		<h4>What if I have multiple accounts?</h4>
		<p>
			All accounts registered to your email address will be located, and you will be given a 
			list of all of those usernames.
		</p>
			
		<h4>What if this cannot find my account?</h4>
		<p>
			It is possible you registered under a different email address.  Please try any other email 
			addresses you have.
		</p>
		</div>
	</fieldset>
	<div class="clear"></div>
	
	<p class="submit"><button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button></p>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
