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
	<legend><span><?php echo Lang::txt('COM_MEMBERS_GROUPS'); ?></span></legend>

	<iframe height="500" name="grouper" id="grouper" src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=groups&tmpl=component&id=' . $this->profile->get('id') . '&t=' . time()); ?>"></iframe>
</fieldset>
