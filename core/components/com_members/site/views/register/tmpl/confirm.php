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
<?php if ($this->getError() && $this->getError() == 'login mismatch') : ?>
	<p class="warning">
		You are currently logged in as <strong><?php echo $this->login; ?></strong>. If you're trying to activate a different account,
		you may do so by <a href="<?php echo $this->redirect; ?>">confirming a different email address</a>.
	</p>
<?php elseif ($this->getError()) : ?>
	<div class="section-inner hz-layout-with-aside">
		<div class="subject">
			<div class="error">
				<h4><?php echo Lang::txt('Invalid Confirmation'); ?></h4>
				<p>The email confirmation link you followed is no longer valid. Your email address "<?php echo $this->escape($this->email); ?>" has not been confirmed.</p>
				<p>Please be sure to click the link from the latest confirmation email received.  Earlier confirmation emails will be invalid. If you cannot locate a newer confirmation email, you may <a href="<?php echo Route::url('index.php?option='.$this->option.'&task=resend'); ?>">resend a new confirmation email</a>.</p>
			</div>
		</div><!-- / .subject -->
		<aside class="aside">
		<h4>Never received or cannot find the confirmation email?</h4>
		<p>You can have a new confirmation email sent to "<?php echo $this->escape($this->email); ?>" by <a href="<?php echo Route::url('index.php?option='.$this->option.'&task=resend&return='.$this->redirect); ?>">clicking here</a>.</p>
	</aside><!-- / .aside -->
	</div>
<?php else : ?>
	<p class="passed">Your email address "<?php echo $this->escape($this->email); ?>" has already been confirmed. You should be able to use <?php echo $this->sitename; ?> now. Thank you.</p>
<?php endif; ?>
</section><!-- / .section -->
