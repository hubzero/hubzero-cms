<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{baseURL: mixed, sitename: mixed, xprofile: mixed} $this
 */
?>
Thank you for updating your account on <?php echo $this->sitename; ?>!

Since you have changed your e-mail address you must click the following
link to confirm your new email address and reactivate your account:
<?php echo $this->baseURL
    . Route::url('index.php?option='
    . $this->option
    . '&controller='
    . $this->controller
    . '&task=confirm&confirm='
    . -$this->xprofile->get('activation')
    . '&email='
    . urlencode($this->xprofile->get('email')), false); ?>

Do not reply to this email.  Replying to this email will not confirm or activate your account.
