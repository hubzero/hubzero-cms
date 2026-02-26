<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;

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
    <?php $val = $this->escape($this->email); ?>
    <?php $val = $this->hubName; ?>
    <p class="passed">
        A confirmation email has been sent to "<?php echo $val; ?>". You must click the link in that email to activate
        your account and resume using <?php echo $val; ?>.
    </p>
    <?php if ($this->show_correction_faq) { ?>
        <h4>Wrong email address?</h4>
        <?php
        $href = Route::url(
            'index.php?option='
            . $this->option
            . '&controller='
            . $this->controller
            . '&task=change&return='
            . $this->return
        );
        ?>
        <p>You can correct your email address by <a href="<?php echo $href; ?>">clicking here</a>.</p>
    <?php } ?>
    <h4>Never received or cannot find the confirmation email?</h4>
    <?php $val = $this->escape($this->email); ?>
    <p>
        You can have a new confirmation email sent to "<?php echo $val; ?>" by <a href="<?php echo
        Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=resend&return=' .
        $this->return); ?>">clicking here</a>.
    </p>
<?php } ?>
</section><!-- / .section -->
