<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<form action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=domovefile&no_html=1'); ?>" method="post" class="hubForm">
	<fieldset>
		<legend><?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE_FILE'); ?></legend>
		<label>
			<?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE_CURRENT_FILE'); ?>: 
			<input type="text" name="file" value="<?php echo $this->escape($this->file); ?>" readonly="readonly" />
		</label>
		<label>
			<?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE_MOVE_TO'); ?>: 
			<?php echo $this->folderList; ?>
		</label>
		<p class="controls">
			<?php echo Html::input('token'); ?>
			<button type="submit" class="btn icon-move"><?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE'); ?></button>
		</p>
	</fieldset>
</form>