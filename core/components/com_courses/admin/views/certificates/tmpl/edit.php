<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_CERTIFICATE') . ': ' . $text, 'courses');
Toolbar::cancel();

Html::behavior('framework');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_UPLOAD'); ?></span></legend>

				<div class="input-wrap">
					<input type="file" name="upload" id="upload" class="required" size="17" />
				</div>
				<div class="input-wrap">
					<input type="submit" value="<?php echo Lang::txt('COM_COURSES_UPLOAD'); ?>" />
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<p><?php echo Lang::txt('COM_COURSES_CERTIFICATE_HELP'); ?></p>
		</div>
	</div>

	<input type="hidden" name="certificate" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="course" value="<?php echo $this->row->get('course_id'); ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="upload" />

	<?php echo Html::input('token'); ?>
</form>
