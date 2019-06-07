<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CREATED'); ?>: <?php echo $this->user->get('registerDate'); ?> UTC
<?php echo Lang::txt('COM_MEMBERS_EMAIL_NAME'); ?>: <?php echo $this->user->get('name'); ?>
<?php echo Lang::txt('COM_MEMBERS_EMAIL_USERNAME'); ?>: <?php echo $this->user->get('username'); ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_APPROVED_MESSAGE', $this->sitename); ?>

Do not reply to this email.  Replies to this email will not be read.
