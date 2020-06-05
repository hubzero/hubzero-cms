<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//get path info
$info = pathinfo($this->folder);
?>

<form action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=dorenamefolder&no_html=1'); ?>" method="post" class="hubForm">
	<fieldset>
		<legend><?php echo Lang::txt('COM_GROUPS_MEDIA_RENAME_FOLDER'); ?></legend>
		<label>
			<?php echo Lang::txt('COM_GROUPS_MEDIA_RENAME_CURRENT_NAME'); ?>:<br />
			<input type="hidden" name="folder" value="<?php echo $this->escape($this->folder); ?>" />

			<input type="text" name="name" value="<?php echo $this->escape($info['basename']); ?>" />
		</label>
		<p class="controls">
			<?php echo Html::input('token'); ?>
			<button type="submit" class="btn icon-edit"><?php echo Lang::txt('COM_GROUPS_MEDIA_RENAME'); ?></button>
		</p>
	</fieldset>
</form>