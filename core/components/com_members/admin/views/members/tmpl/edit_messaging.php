<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="adminform">
	<legend><span><?php echo Lang::txt('COM_MEMBERS_MENU_MESSAGING'); ?></span></legend>

	<iframe height="500" name="messaging" id="messaging-settings" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=messages&tmpl=component&task=settings&id=' . $this->profile->get('id') . '&t=' . time()); ?>"></iframe>
</fieldset>
