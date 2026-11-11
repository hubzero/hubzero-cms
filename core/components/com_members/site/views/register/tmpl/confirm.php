<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$this
     ->css('register')
     ->js('register');
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
<?php if ($this->getError() && $this->getError() == 'login mismatch') : ?>
    <p class="warning">
        You are currently logged in as <strong><?= $this->escape($this->login) ?></strong>. If you're trying to activate a different account,
        you may do so by <a href="<?= $this->escape($this->redirect) ?>">confirming a different email address</a>.
    </p>
<?php elseif ($this->getError()) : ?>
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
            <div class="error">
                <h4><?php echo Lang::txt('Invalid Confirmation'); ?></h4>
                <p>The email confirmation link you followed is no longer valid. Your email address "<?= $this->escape($this->email) ?>" has not been confirmed.</p>
                <?php
                $resendUrl = Route::url(
                    'index.php?option=' . $this->option . '&task=resend&email=' . urlencode((string) $this->email),
                );
                ?>
                <p>Please be sure to click the link from the latest confirmation email received.  Earlier confirmation emails will be invalid. If you cannot locate a newer confirmation email, you may <a href="<?= $resendUrl ?>">resend a new confirmation email</a>.</p>
            </div>
        </div><!-- / .subject -->
        <aside class="aside">
        <h4>Never received or cannot find the confirmation email?</h4>
        <?php
        $resendUrl = Route::url(
            'index.php?option=' . $this->option . '&task=resend&email=' . urlencode((string) $this->email) . '&return='
                . $this->redirect,
        );
        ?>
        <p>You can have a new confirmation email sent to "<?= $this->escape($this->email) ?>" by <a href="<?= $resendUrl ?>">clicking here</a>.</p>
    </aside><!-- / .aside -->
    </div>
<?php elseif (!empty($this->justConfirmed)) : ?>
    <p class="passed">Thank you! Your email address "<?= $this->escape($this->email) ?>" has been confirmed and your account is now active. Please <a href="<?= $this->escape($this->loginUrl) ?>">log in</a> to start using <?= $this->escape($this->sitename) ?>.</p>
<?php else : ?>
    <p class="passed">Your email address "<?= $this->escape($this->email) ?>" has already been confirmed. You should be able to use <?= $this->sitename ?> now. Thank you.</p>
<?php endif; ?>
</section><!-- / .section -->
