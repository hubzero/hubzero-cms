<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=medialist&file=' . urlencode($this->file)); ?>" id="component-form" method="post" name="adminForm" autocomplete="off">
	<fieldset>
		<h2 class="modal-title">
			<?php echo Lang::txt('COM_MEDIA_FILE_LINK'); ?>
		</h2>
	</fieldset>
	<div class="manager">
		<input type="text" value="<?php echo $this->escape($this->file); ?>" name="path" />

		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	</div>
</form>
