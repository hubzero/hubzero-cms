<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="overlay"></div>
<div id="questions" data-redirect="true">
    <p>
        <?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_THANK_YOU'); ?>
        <?php if ($award) : ?>
            <?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_AWARDS_EARNED', $award); ?>
        <?php endif; ?>
        <?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_REDIRECTED_SOON'); ?>
    </p>

    <?php
    $redirectUri = Request::getString(
        'REQUEST_URI',
        Request::getString('REDIRECT_REQUEST_URI', '', 'server'),
        'server'
    );
    ?>
    <a href="<?php echo $redirectUri; ?>">
        <?php echo Lang::txt('MOD_INCREMENTAL_REGISTRATION_CLICK_IF_NOT_REDIRECTED'); ?>
    </a>
</div>
