<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

Hello <?php echo $this->users->first()->name; ?>,

A username reminder has been requested for your <?php echo $this->config->get('sitename'); ?> account.

The following usernames are associated with this email address:

<?php foreach ($this->users as $user) : ?>
<?php echo $user->username; ?>
<?php endforeach; ?>


Thank you!