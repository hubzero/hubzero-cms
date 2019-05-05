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

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ROLES') . ': ' . $text, 'courses.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-offering_id"><?php echo Lang::txt('COM_COURSES_OFFERING'); ?>:</label><br />
					<select name="fields[offering_id]" id="field-offering_id">
						<option value="0"<?php if (0 == $this->row->offering_id) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_NONE'); ?></option>
						<?php foreach ($this->courses as $course) { ?>
								<optgroup label="<?php echo $course->get('alias'); ?>">
							<?php foreach ($course->offerings() as $offering) { ?>
									<option value="<?php echo $offering->get('id'); ?>"<?php if ($offering->get('id') == $this->row->offering_id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('title'))); ?></option>
							<?php } ?>
								</optgroup>
						<?php } ?>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="required" size="50" value="<?php echo $this->escape($this->row->title); ?>" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" size="50" value="<?php echo $this->escape($this->row->alias); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
		</div>
		<div class="col span4">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_COURSES_FIELD_OFFERING'); ?></th>
						<td>
							<?php echo $this->row->offering_id; ?>
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_COURSES_FIELD_ID'); ?></th>
						<td>
							<?php echo $this->row->id; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>