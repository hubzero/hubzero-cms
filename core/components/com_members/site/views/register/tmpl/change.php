<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('register')
     ->js('register');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->success) { ?>
	<p class="passed"><?php echo Lang::txt('Your account has been updated successfully.'); ?></p>
<?php } else { ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=change'); ?>" method="post" id="hubForm">
	<?php if (($this->email_confirmed != 1) && ($this->email_confirmed != 3)) { ?>
		<div class="explaination">
			<h4>Never received or cannot find the confirmation email?</h4>
			<p>You can have a new confirmation email sent to "<?php echo $this->escape($this->email); ?>" by <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=resend&return=' . $this->return); ?>">clicking here</a>.</p>
		</div>
	<?php } ?>
		<fieldset>
			<h3><?php echo Lang::txt('Correct Email Address'); ?></h3>
			<label<?php if (!$this->email || !\Components\Members\Helpers\Utility::validemail($this->email)) { echo ' class="fieldWithErrors"'; } ?>>
				<?php echo Lang::txt('Valid E-mail:'); ?>
				<input name="email" id="email" type="text" size="51" value="<?php echo $this->escape($this->email); ?>" />
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="change" />
		<input type="hidden" name="act" value="show" />

		<p class="submit"><input type="submit" name="update" value="<?php echo Lang::txt('Update Email'); ?>" /></p>
	</form>
<?php } ?>
</section><!-- / .section -->
