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
	<div class="section-inner hz-layout-with-aside">
		<div class="subject">
			<p class="error">
				Your email address "<?php echo $this->escape($this->email); ?>" has not been confirmed. Please check your email for a confirmation notice. You must click the link in that email to activate your account and resume using <?php echo $this->sitename; ?>.
			</p>
		</div><!-- / .subject -->
		<aside class="aside">
		<h4>Never received or cannot find the confirmation email?</h4>
		<p>You can have a new confirmation email sent to "<?php echo $this->escape($this->email); ?>" by <a href="<?php echo Route::url('index.php?option=com_members&controller=register&task=resend&return=' . $this->return); ?>">clicking here</a>.</p>
	</aside><!-- / .aside -->
	</div>
</section><!-- / .section -->
