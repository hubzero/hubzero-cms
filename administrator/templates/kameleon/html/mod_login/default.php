<?php
// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login">
	<fieldset>
		<legend><?php echo JText::_('MOD_LOGIN_LOGIN'); ?></legend>

		<label id="mod-login-username-lbl" for="mod-login-username">
			<span><?php echo JText::_('JGLOBAL_USERNAME'); ?></span>
			<input name="username" id="mod-login-username" class="input-username" type="text" size="15" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" />
		</label>

		<label id="mod-login-password-lbl" for="mod-login-password">
			<span><?php echo JText::_('JGLOBAL_PASSWORD'); ?></span>
			<input name="passwd" id="mod-login-password" class="input-password" type="password" size="15" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" />
		</label>

		<div class="button-holder">
			<input type="submit" class="btn" value="<?php echo JText::_('MOD_LOGIN_LOGIN'); ?>" />
		</div>

		<input type="hidden" name="option" value="com_login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />

		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
