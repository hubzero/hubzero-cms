<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->baseURL = rtrim($this->baseURL, '/');

$confirmationCode = -$this->confirm;
$userEmail = urlencode($this->email);
$relativeUrl = "index.php?option=$this->option&task=confirm&confirm=$confirmationCode&email=$userEmail";
$link = $this->baseURL . Route::urlForClient('site', $relativeUrl, false);
$link = str_replace('/administrator', '', $link);
?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CREATED') . ": $this->registerDate (UTC)"; ?>
<?php echo Lang::txt('COM_MEMBERS_EMAIL_NAME') . ": $this->name"; ?>
<?php echo Lang::txt('COM_MEMBERS_EMAIL_USERNAME') . ": $this->login"; ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_MESSAGE', $this->sitename); ?>

<?php echo $link; ?>

<?php echo Lang::txt('COM_MEMBERS_EMAIL_CONFIRM_DO_NOT_REPLY');
