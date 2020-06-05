<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
//Toolbar::title(Lang::txt('COM_NEWSLETTER'), 'newsletter.png');
?>

<p class="warning">
	<?php echo Lang::txt('COM_NEWSLETTER_DEPENDENCY'); ?>
	<br />
	<a href="<?php echo Route::url('index.php?option=com_cron'); ?>"><?php echo Lang::txt('COM_NEWSLETTER_CHECK_CRON'); ?></a>
</p>
