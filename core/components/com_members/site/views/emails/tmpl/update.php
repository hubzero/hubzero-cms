<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
Thank you for updating your account on <?php echo $this->sitename; ?>!

Since you have changed your e-mail address you must click the following link to confirm your new email address and reactivate your account:
<?php echo $this->baseURL . Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=confirm&confirm=' . -$this->xprofile->get('activation') . '&email=' . urlencode($this->xprofile->get('email'))); ?>

Do not reply to this email.  Replying to this email will not confirm or activate your account.
