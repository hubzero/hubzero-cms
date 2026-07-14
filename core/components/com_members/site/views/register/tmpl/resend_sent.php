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
	<p class="passed">If an account matching that email address or username exists and still needs verification, a new confirmation email has been sent. Please check your inbox (and your spam folder) and click the link to activate your account on <?php echo $this->escape($this->hubName); ?>.</p>
	<p>Didn't receive it? You can <a href="<?php echo Route::url('index.php?option=com_members&controller=register&task=resend'); ?>">request another confirmation email</a> in a few minutes.</p>
</section><!-- / .section -->
