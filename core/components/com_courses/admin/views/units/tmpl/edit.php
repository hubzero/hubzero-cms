<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$canDo = \Components\Courses\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_UNITS') . ': ' . $text, 'courses');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('modal');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->offering->get('id'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
				<input type="hidden" name="task" value="save" />

				<div class="input-wrap">
					<label for="offering_id"><?php echo Lang::txt('COM_COURSES_FIELD_OFFERING'); ?>:</label><br />
					<select name="fields[offering_id]" id="offering_id">
						<option value="-1"><?php echo Lang::txt('COM_COURSES_SELECT'); ?></option>
						<?php
						require_once Component::path('com_courses') . DS . 'models' . DS . 'courses.php';
						$model = \Components\Courses\Models\Courses::getInstance();
						if ($model->courses()->total() > 0)
						{
							foreach ($model->courses() as $course)
							{
								?>
								<optgroup label="<?php echo $this->escape(stripslashes($course->get('alias'))); ?>">
								<?php
								$j = 0;
								foreach ($course->offerings() as $i => $offering)
								{
									?>
									<option value="<?php echo $this->escape(stripslashes($offering->get('id'))); ?>"<?php if ($offering->get('id') == $this->row->get('offering_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($offering->get('alias'))); ?></option>
									<?php
								}
								?>
								</optgroup>
								<?php
							}
						}
						?>
					</select>
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_COURSES_FIELD_ALIAS_HINT'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_COURSES_FIELD_DESCRIPTION'); ?>:</label><br />
					<textarea name="fields[description]" id="field-description" cols="35" rows="20"><?php echo $this->escape(stripslashes($this->row->get('description'))); ?></textarea>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_ASSETS'); ?></span></legend>
				<?php if ($this->row->get('id')) { ?>
					<iframe width="100%" height="400" name="assets" id="assets" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=assets&tmpl=component&scope=unit&scope_id=' . $this->row->get('id') . '&course_id=' . $this->offering->get('course_id')); ?>"></iframe>
				<?php } else { ?>
					<p><?php echo Lang::txt('COM_COURSES_ENTRY_MUST_BE_SAVED_BEFORE_ASSETS'); ?></p>
				<?php } ?>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('id')); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_ORDERING'); ?></th>
						<td><?php echo $this->escape($this->row->get('ordering')); ?></td>
					</tr>
					<?php if ($this->row->get('created')) { ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_CREATED'); ?></th>
							<td><time datetime="<?php echo $this->escape($this->row->get('created')); ?>"><?php echo $this->escape(Date::of($this->row->get('created'))->toLocal()); ?></time></td>
						</tr>
					<?php } ?>
					<?php if ($this->row->get('created_by')) { ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_CREATOR'); ?></th>
							<td><?php
							$creator = User::getInstance($this->row->get('created_by'));
							echo $this->escape(stripslashes($creator->get('name'))); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_COURSES_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
						<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>
