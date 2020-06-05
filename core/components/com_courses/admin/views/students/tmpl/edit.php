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

Toolbar::title(Lang::txt('COM_COURSES') .': ' . $text . ' ' . Lang::txt('COM_COURSES_STUDENT'), 'courses.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$profile = User::getInstance($this->row->get('user_id'));

$js = '';

$role_id = 0;
$roles = $this->offering->roles();
foreach ($roles as $role)
{
	if ($role->alias == 'student')
	{
		$role_id = $role->id;
		break;
	}
}
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="offering" value="<?php echo $this->row->get('offering_id'); ?>" />
				<input type="hidden" name="section" value="<?php echo $this->row->get('section_id'); ?>" />
				<input type="hidden" name="fields[role_id]" value="<?php echo $this->row->get('role_id'); ?>" />
				<input type="hidden" name="fields[user_id]" value="<?php echo $this->row->get('user_id'); ?>" />

				<div class="input-wrap">
					<label for="field-offering_id"><?php echo Lang::txt('COM_COURSES_OFFERING'); ?>:</label><br />
					<select name="fields[offering_id]" id="field-offering_id" class="required" onchange="changeDynaList('section_id', offeringsections, document.getElementById('offering_id').options[document.getElementById('offering_id').selectedIndex].value, 0, 0);">
						<option value="-1"><?php echo Lang::txt('COM_COURSES_NONE'); ?></option>
						<?php
						require_once Component::path('com_courses') . DS . 'models' . DS . 'courses.php';
						$model = \Components\Courses\Models\Courses::getInstance();
						$data = array();
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
									foreach ($offering->sections() as $section)
									{
										$data[$j++] = array($offering->get('id'), $section->get('id'), $section->get('title'));
									}
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
				<div class="input-wrap">
					<label for="field-section_id"><?php echo Lang::txt('COM_COURSES_SECTION'); ?>:</label><br />
					<select name="fields[section_id]" id="field-section_id">
						<option value="-1"><?php echo Lang::txt('COM_COURSES_SELECT'); ?></option>
						<?php foreach ($this->offering->sections() as $k => $section) { ?>
							<option value="<?php echo $this->escape(stripslashes($section->get('id'))); ?>"<?php if ($section->get('id') == $this->row->get('section_id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-enrolled"><?php echo Lang::txt('COM_COURSES_FIELD_ENROLLED'); ?></label><br />
					<?php echo Html::input('calendar', 'fields[enrolled]', $this->row->get('enrolled'), array('id' => 'field-enrolled')); ?>
				</div>
				<div class="input-wrap">
					<label for="field-token"><?php echo Lang::txt('COM_COURSES_FIELD_SERIAL_NUM'); ?></label><br />
					<input type="text" name="fields[token]" id="field-token" value="<?php echo $this->escape($this->row->get('token')); ?>" />
				</div>
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
						<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_USER_ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('user_id')); ?></td>
					</tr>
					<?php if ($profile) { ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_NAME'); ?></th>
							<td><?php echo $this->escape(stripslashes($profile->get('name'))); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_USERNAME'); ?></th>
							<td><?php echo $this->escape(stripslashes($profile->get('username'))); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_COURSES_FIELD_EMAIL'); ?></th>
							<td><?php echo $this->escape(stripslashes($profile->get('email'))); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<script type="application/json" id="offering-data">
		{
			"data": <?php echo json_encode($data); ?>
		}
	</script>

	<?php echo Html::input('token'); ?>
</form>
