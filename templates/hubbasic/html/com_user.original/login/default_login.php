<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off'):
		JFactory::getApplication()->redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		JError::raiseError(403, 'Forbidden: SSL is required to view this resource');
endif; ?>
<?php if(JPluginHelper::isEnabled('authentication', 'openid')) :
		$lang = &JFactory::getLanguage();
		$lang->load('plg_authentication_openid', JPATH_ADMINISTRATOR);
		$langScript =   'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_('WHAT_IS_OPENID').'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_('LOGIN_WITH_OPENID').'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_('NORMAL_LOGIN').'\';'.
						' var comlogin = 1;';
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration($langScript);
		JHTML::_('script', 'openid.js');
endif; ?>

<?php
$jconfig =& JFactory::getConfig();
$sitename = $jconfig->getValue('config.sitename');
echo $this->params->get('type');
?>

	<form action="<?php echo JRoute::_('index.php', true, $this->params->get('usesecure')); ?>"  method="post" name="com-login" id="hubForm<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" >
		<div class="explaination">
		<?php
				$usersConfig = &JComponentHelper::getParams('com_users');
				if ($usersConfig->get('allowUserRegistration')) : 
		?>
			<h4>No account?</h4>
			<p><a href="<?php echo JRoute::_('index.php?option=com_register'); ?>"><?php echo JText::_('REGISTER'); ?></a>. It's free!</p>

			<h4>Is this really free?</h4>
			<p>Yes! Use of <?php echo $sitename; ?> resources and tools is <em>free</em> for registered users. There are no hidden costs or fees.</p>

			<h4>Why is registration required for parts of <?php echo $sitename; ?>?</h4>
			<p>Our sponsors ask us who uses <?php echo $sitename; ?> and what they use it for. Registration
			helps us answer these questions. Usage statistics also focus our attention on improvements, making the
			<?php echo $sitename; ?> experience better for <em>you</em>.</p>
		<?php endif; ?>

		</div>
		<fieldset>
		<?php if ($this->params->get('description_login')) : ?>
			<legend class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->params->get('description_login_text'); ?></legend>
		<?php // echo $this->image; ?>
		<?php endif; ?>

			<label for="username">
				<?php echo JText::_('Username'); ?>:
				<input name="username" id="username" type="text" tabindex="1" class="inputbox" alt="username" size="18" />
			</label>

			<p class="hint">
				<a href="<?php echo JRoute::_('index.php?option=com_user&view=remind'); ?>"><?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
			</p>

			<label for="passwd">
				<?php echo JText::_('_PASSWORD'); ?>:
				<input type="password" tabindex="2" name="passwd" id="passwd" />
			</label>

			<p class="hint">
				<a href="<?php echo JRoute::_('index.php?option=com_user&view=reset'); ?>">
				<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
			</p>

		<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
			<label for="remember">
				<input type="checkbox" class="option" name="remember" id="remember" value="yes" alt="Remember Me" />
				<?php echo JText::_('Remember me'); ?>
			</label>
		<?php endif; ?>

			<input type="hidden" name="option" value="com_user" />
			<input type="hidden" name="task" value="login" />
			<input type="hidden" name="freturn" value="<?php echo  base64_encode( $_SERVER['REQUEST_URI']); ?>" />
			<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
			<?php echo JHTML::_('form.token'); ?>
		</fieldset>
		<div class="clear"></div>

		<p class="submit">
			<input type="submit" name="Submit" class="button" value="<?php echo JText::_('LOGIN'); ?>" />
		</p>
	</form>

<?php
		if (!empty($this->error_message))
		{
			echo '<p class="error">'. $this->error_message . '</p>';
		}
		if (!empty($this->login_attempts) && $this->login_attempts >= 2)
		{
			echo '<p class="hint">Having trouble logging in? <a href="support/report_problems/">Report problems to Support</a>.</p>';
		}
?>