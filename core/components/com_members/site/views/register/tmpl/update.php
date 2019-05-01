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
</header><!-- / #content-header -->

<section class="main section">

<?php if (isset($this->self) && $this->self) { ?>
	<p class="passed">Your account has been updated successfully.</p>
	<?php if ($this->updateEmail) { ?>
		<p>Thank you for updating your account. In order to continue to use this account you must verify your new email address.</p>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } else { ?>
			<p>A confirmation email has been sent to <?php echo $this->xprofile->get('email'); ?>. You must click the link in that email to activate your account and begin using <?php echo $this->sitename; ?>.</p>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<p class="passed">The account has been updated successfully.</p>
	<?php if ($this->updateEmail) { ?>
		<p>The user of this account has been notified of the change. In order to continue to use this account they will need to verify the new email address.</p>
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } else { ?>
			<p>A confirmation email has been sent to <?php echo $this->xprofile->get('email'); ?>. They must click the link in that email to activate your account and begin using <?php echo $this->sitename; ?>.</p>
		<?php } ?>
	<?php } ?>
<?php } ?>
</section><!-- / .main section -->
