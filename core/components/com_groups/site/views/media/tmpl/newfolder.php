<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<form action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=savefolder&no_html=1'); ?>" method="post" class="hubForm">
	<fieldset>
		<legend><?php echo Lang::txt('Add Folder'); ?></legend>
		<label>
			<?php echo Lang::txt('Folder Name: '); ?>
			<input type="text" name="name" />
		</label>
		<label>
			<?php echo Lang::txt('Create in: '); ?>
			<?php echo $this->folderList; ?>
		</label>
		<p class="controls">
			<?php echo Html::input('token'); ?>
			<button type="submit" class="btn icon-save"><?php echo Lang::txt('Create'); ?></button>
		</p>
	</fieldset>
</form>