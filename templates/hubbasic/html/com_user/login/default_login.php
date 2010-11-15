<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
echo $this->params->get('type');
?>

	<form action="<?php echo JRoute::_( 'index.php', true, $this->params->get('usesecure')); ?>"  method="post" name="com-login" id="hubForm<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" >
        <div class="explaination">
		<?php
                $usersConfig = &JComponentHelper::getParams( 'com_users' );
                if ($usersConfig->get('allowUserRegistration')) : ?>
                <h4>No account?</h4><p><a href="<?php echo JRoute::_( 'index.php?option=com_user&view=register' ); ?>">
                <?php echo JText::_('REGISTER'); ?></a>. It's free!</p>

                <h4>Is this really free?</h4>
                <p>Yes! Use of <?php echo $this->hubShortName;?> resources and tools is <em>free</em> for registered users. There are no hidden costs or fees.</p>

                <h4>Why is registration required for parts of the <?php echo $this->hubShortName; ?>?</h4>

                <p>Our sponsors ask us who uses the <?php echo $this->hubShortName;?> and what they use it for. Registration
                helps us answer these questions. Usage statistics also focus our attention on improvements, making the
                <?php echo $this->hubShortName; ?> experience better for <em>you</em>.</p>
                <?php endif; ?>

        </div>
        <fieldset>
		<?php if ( $this->params->get( 'description_login' ) ) : ?>
		<h3 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $this->params->get( 'description_login_text' ); ?></h3>
		<?php // echo $this->image; ?>
		<?php endif; ?>

                <label>
                        <?php echo JText::_('Username'); ?>:
			<input name="username" id="username" type="text" tabindex="1" class="inputbox" alt="username" size="18" />
                </label>

                <p class="hint">
			<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>">
			<?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></a>
                </p>

                <label>
                        <?php echo JText::_('_PASSWORD'); ?>:
                        <input type="password" tabindex="2" name="passwd" id="passwd" />
                </label>

                <p class="hint">
			<a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>">
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
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
        </fieldset>
        <div class="clear"></div>
        <p class="submit"><input type="submit" name="Submit" class="button" value="<?php echo JText::_('LOGIN'); ?>" /></p>
</form>

<?php
        if (!empty($this->error_message))
                echo '<p class="error">'. $this->error_message . '</p>';
        if (!empty($this->login_attempts) && $this->login_attempts >= 2)
                echo '<p class="hint">Having trouble logging in? <a href="support/report_problems/">Report problems to Support</a>.</p>';
?>

