<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Courses\Helpers\Permissions::getActions();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_PAGES') . ': ' . $text, 'courses');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('page');

Html::behavior('framework');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="course" value="<?php echo $this->course->get('id'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->offering->get('id'); ?>" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="fields[course_id]" value="<?php echo $this->course->get('id'); ?>" />
				<input type="hidden" name="fields[offering_id]" value="<?php echo $this->row->get('offering_id'); ?>" />

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="required" value="<?php echo $this->escape($this->row->get('title')); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-url"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape($this->row->get('url')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="field-content"><?php echo Lang::txt('COM_COURSES_FIELD_CONTENT'); ?>:</label><br />
					<?php echo $this->editor('fields[content]', $this->escape($this->row->content('raw')), 50, 30, 'field-content', array('class' => 'required')); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span4">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_COURSES_FIELD_TYPE'); ?></th>
						<?php if ($this->row->get('course_id')) { ?>
							<?php if ($this->row->get('offering_id')) { ?>
								<td><?php echo Lang::txt('COM_COURSES_PAGES_OFFERING'); ?></td>
							<?php } else { ?>
								<td><?php echo Lang::txt('COM_COURSES_PAGES_COURSE'); ?></td>
							<?php } ?>
						<?php } else { ?>
							<td><?php echo Lang::txt('COM_COURSES_PAGES_USER_GUIDE'); ?></td>
						<?php } ?>
					</tr>
					<?php if ($this->row->get('course_id')) { ?>
						<tr>
							<th><?php echo Lang::txt('COM_COURSES_FIELD_COURSE_ID'); ?></th>
							<td><?php echo $this->escape($this->row->get('course_id')); ?></td>
						</tr>
					<?php } ?>
					<?php if ($this->row->get('offering_id')) { ?>
						<tr>
							<th><?php echo Lang::txt('COM_COURSES_FIELD_OFFERING_ID'); ?></th>
							<td><?php echo $this->escape($this->row->get('offering_id')); ?></td>
						</tr>
					<?php } ?>
					<tr>
						<th><?php echo Lang::txt('COM_COURSES_FIELD_ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('id')); ?></td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-active"><?php echo Lang::txt('COM_COURSES_FIELD_ACTIVE'); ?>:</label><br />
					<select name="fields[active]" id="field-active">
						<option value="1" <?php if ($this->row->get('active')) { echo 'selected="selected"'; } ?>><?php echo Lang::txt('JYES'); ?></option>
						<option value="0" <?php if (!$this->row->get('active')) { echo 'selected="selected"'; } ?>><?php echo Lang::txt('JNO'); ?></option>
					</select>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<?php if (!$this->row->get('id')) { ?>
					<p><?php echo Lang::txt('COM_COURSES_UPLOAD_ADDED_LATER'); ?></p>
				<?php } else { ?>
					<iframe width="100%" height="300" name="filelist" id="filelist" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=pages&task=files&tmpl=component&listdir=' . $this->row->get('offering_id') . '&course=' . $this->course->get('id')); ?>"></iframe>
				<?php } ?>
			</fieldset>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>