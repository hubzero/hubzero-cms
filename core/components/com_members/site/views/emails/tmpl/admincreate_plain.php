<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{baseUrl: mixed, sitename: mixed, xprofile: mixed} $this
 */
?>

<?php echo $this->xprofile->get('name');
if ($this->xprofile->get('orginization')) {
    echo ' / ' . $this->xprofile->get('orginization');
} ?> (<?php echo $this->xprofile->get('email'); ?>) has requested the new account
'<?php echo $this->xprofile->get('username'); ?>' on <?php echo $this->sitename; ?>.

Click the following link to review this user's account:
<?php echo $this->baseUrl . Route::url($this->xprofile->link());
