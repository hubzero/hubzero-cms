<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = str_replace('/administrator', '', Request::base());
?>

<?php echo strip_tags(Lang::txt('PLG_USER_HUBZERO_EMAIL_ADMIN_ACCOUNT_REQUESTED', $this->user['name'] . ' (' . $this->user['email'] . ')', $this->user['username'], $this->sitename)); ?>

<?php echo Lang::txt('PLG_USER_HUBZERO_EMAIL_ADMIN_REVIEW_LINK'); ?>
<?php echo rtrim($base, '/') . Route::url('index.php?option=com_members&id=' . $this->user['id']);
