<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{baseUrl: mixed, config: mixed, user: mixed} $this
 */
?>

Hello <?php echo $this->user->name; ?>,

<?php
echo 'A request has been made to reset your ' . $this->config->get('sitename') . ' account password. '
    . 'This account has had no web sign-in for a long time, so password sign-in, password resets '
    . 'and SSH/SFTP access have been disabled.' . "\n\n";
echo 'If you can sign in another way (for example through your institution), doing so restores access. '
    . 'Otherwise contact support.' . "\n";
?>

<?php echo $this->baseUrl; ?>


Thank you!