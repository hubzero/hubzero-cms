<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

Hello <?php echo $this->user->name; ?>,

A request has been made to reset your <?php echo $this->config->get('sitename'); ?> account password. This account has had no web sign-in for a long time, so password sign-in, password resets and SSH/SFTP access have been disabled.

If you can sign in another way (for example through your institution), doing so restores access. Otherwise contact support.

<?php echo $this->baseUrl; ?>


Thank you!