<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{config: mixed, users: mixed} $this
 */
?>

Hello <?php echo $this->users->first()->name; ?>,

A username reminder has been requested for your <?php echo $this->config->get('sitename'); ?> account.

The following usernames are associated with this email address:

<?php foreach ($this->users as $user) : ?>
    <?php echo $user->username; ?>
<?php endforeach; ?>


Thank you!