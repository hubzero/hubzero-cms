<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('register')
     ->js('register');
?>
<header id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>
</header>

<section class="main section">
<?php if ($this->getError()) { ?>
	<p class="error" role="alert"><?php echo $this->escape($this->getError()); ?></p>
<?php } ?>
	<p>Enter the email address or username for your <?php echo $this->escape($this->hubName); ?> account and we'll send a new confirmation link. You do not need to be logged in.</p>

	<form method="post" id="hubForm" action="<?php echo Route::url('index.php?option=com_members&controller=register&task=resend'); ?>">
		<fieldset>
			<label for="identifier">Email address or username
				<input type="text" class="form-control" name="identifier" id="identifier" value="<?php echo $this->escape($this->identifier); ?>" autocomplete="email" required />
			</label>

			<?php // Honeypot -- hidden by #botcheck-label in register.css; only bots fill it in. ?>
			<label id="botcheck-label" for="botcheck">
				Leave this field blank
				<input type="text" class="form-control" name="botcheck" id="botcheck" value="" tabindex="-1" autocomplete="off" />
			</label>

			<?php
			$captchas = Event::trigger('captcha.onDisplay');
			if (count($captchas) > 0)
			{
				echo '<div class="captcha">' . implode("\n", $captchas) . '</div>';
			}
			?>

			<p class="submit">
				<button type="submit" class="btn btn-primary">Send confirmation email</button>
			</p>
		</fieldset>

		<input type="hidden" name="option" value="com_members" />
		<input type="hidden" name="controller" value="register" />
		<input type="hidden" name="task" value="resend" />
		<?php echo Html::input('token'); ?>
	</form>
</section><!-- / .section -->
