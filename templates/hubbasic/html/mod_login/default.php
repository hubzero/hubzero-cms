<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

ximport('Hubzero_Document');
Hubzero_Document::addComponentStylesheet('com_user');

$jconfig =& JFactory::getConfig();
$sitename = $jconfig->getValue('config.sitename');
r
?>

<form action="<?php echo JRoute::_( 'index.php', true ); ?>"  method="post" name="com-login" id="hubForm" >
	<div class="explaination">
	<?php
			$usersConfig = &JComponentHelper::getParams( 'com_users' );
			if ($usersConfig->get('allowUserRegistration')) : ?>
			<h4>No account?</h4><p><a href="/register">
			<?php echo JText::_('REGISTER'); ?></a>. It's free!</p>

			<h4>Is this really free?</h4>
			<p>Yes! Use of <?php echo $sitename; ?> resources and tools is <em>free</em> for registered users. There are no hidden costs or fees.</p>

			<h4>Why is registration required for parts of <?php echo $sitename; ?>?</h4>

			<p>Our sponsors ask us who uses <?php echo $sitename; ?> and what they use it for. Registration
			helps us answer these questions. Usage statistics also focus our attention on improvements, making the
			<?php echo $sitename; ?> experience better for <em>you</em>.</p>
			<?php endif; ?>

	</div>
	<fieldset>
		<h3 class="componentheading">Log in with your Hub account.</h3>
			<label>
					<?php echo JText::_('Username'); ?>:
		<input name="username" id="username" type="text" tabindex="1" class="inputbox" alt="username" size="18" />
			</label>

			<p class="hint">
		<a href="/login/remind">
		<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
			</p>

			<label>
					<?php echo JText::_('_PASSWORD'); ?>:
					<input type="password" tabindex="2" name="passwd" id="passwd" />
			</label>

			<p class="hint">
		<a href="/login/reset">
		<?php echo JText::_('FORGOT_YOUR_PASSWORD'); ?></a>
	</p>

<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
			<label>
					<input type="checkbox" class="option" name="remember" id="remember" value="yes" alt="Remember Me" />
					<?php echo JText::_('Remember me'); ?>
			</label>
<?php endif; ?>

	<input type="hidden" name="option" value="com_user" />
	<input type="hidden" name="task" value="login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</fieldset>
	<div class="clear"></div>
	<p class="submit"><input type="submit" name="Submit" class="button" value="<?php echo JText::_('LOGIN'); ?>" /></p>
</form>
