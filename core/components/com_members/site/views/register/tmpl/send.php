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
<?php } else { ?>
	<p class="passed">A confirmation email has been sent to "<?php echo $this->escape($this->email); ?>".  You must click the link in that email to activate your account and resume using <?php echo $this->hubName; ?>.</p>
	<?php if ($this->show_correction_faq) { ?>
		<h4>Wrong email address?</h4>
		<p>You can correct your email address by <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=change&return=' . $this->return); ?>">clicking here</a>.</p>
	<?php } ?>
	<h4>Never received or cannot find the confirmation email?</h4>
	<p>You can have a new confirmation email sent to "<?php echo $this->escape($this->email); ?>" by <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=resend&return=' . $this->return); ?>">clicking here</a>.</p>
<?php } ?>
</section><!-- / .section -->
