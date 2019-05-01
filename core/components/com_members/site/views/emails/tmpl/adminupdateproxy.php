<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<?php echo $this->xprofile->get('name');
if ($this->xprofile->get('orginization')) {
	echo ' / ' . $this->xprofile->get('orginization');
} ?> (<?php echo $this->xprofile->get('email'); ?>)'s account '<?php echo $this->xprofile->get('username'); ?>' on <?php echo $this->sitename; ?> has been edited by an adminnistrator.

Click the following link to review this user's account:
<?php echo $this->baseURL . Route::url($this->xprofile->link());
