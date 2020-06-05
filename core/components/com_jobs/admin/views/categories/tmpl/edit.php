<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Jobs\Helpers\Permissions::getActions('category');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_JOBS') . ': ' . Lang::txt('COM_JOBS_CATEGORIES') . ': ' . $text, 'category');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="item-form" name="adminForm" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<?php if ($this->task == 'edit') { ?>
	<p class="warning">
		<?php echo Lang::txt('COM_JOBS_WARNING_EDIT_CATEGORY'); ?>
	</p>
	<?php } ?>
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

		<div class="input-wrap">
			<label for="category"><?php echo Lang::txt('COM_JOBS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
			<input type="text" name="category" id="category" class="required" maxlength="100" value="<?php echo $this->escape(stripslashes($this->row->category)); ?>" />
		</div>
		<div class="input-wrap">
			<label for="description"><?php echo Lang::txt('COM_JOBS_FIELD_DESCRIPTION'); ?>: </label>
			<input type="text" name="description" id="description"  maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
		</div>

		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
	</fieldset>

	<?php echo Html::input('token'); ?>
</form>