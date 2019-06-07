<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->baseURL = rtrim($this->baseURL, '/');

$link = $this->baseURL . Route::urlForClient('site', 'index.php?option=' . $this->option . '&task=confirm&confirm=' . -$this->xprofile->get('activation') . '&email=' . urlencode($this->xprofile->get('email')));

//$link = $this->baseURL . $link;
$link = str_replace('/administrator', '', $link);
?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CREATED'); ?>: <?php echo $this->xprofile->get('registerDate'); ?> (UTC)
<?php echo Lang::txt('COM_MEMBERS_EMAIL_NAME'); ?>: <?php echo $this->xprofile->get('name'); ?>
<?php echo Lang::txt('COM_MEMBERS_EMAIL_USERNAME'); ?>: <?php echo $this->xprofile->get('username'); ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_MESSAGE', $this->sitename); ?>

<?php echo $link; ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_DO_NOT_REPLY');
