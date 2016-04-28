<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	if ($('#offering_id').val() == '') {
		alert('<?php echo Lang::txt('COM_COURSES_ERROR_MISSING_OFFERING'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
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
					<select name="fields[offering_id]" id="field-offering_id" onchange="changeDynaList('section_id', offeringsections, document.getElementById('offering_id').options[document.getElementById('offering_id').selectedIndex].value, 0, 0);">
						<option value="-1"><?php echo Lang::txt('COM_COURSES_NONE'); ?></option>
						<?php
						require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'courses.php');
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
									foreach ($offering->sections() as $section)
									{
										$js .= 'offeringsections[' . $j++ . "] = new Array( '" . $offering->get('id') . "','" . addslashes($section->get('id')) . "','" . addslashes($section->get('title')) . "' );\n\t\t";
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
						<th><?php echo Lang::txt('COM_COURSES_FIELD_ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('id')); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_COURSES_FIELD_USER_ID'); ?></th>
						<td><?php echo $this->escape($this->row->get('user_id')); ?></td>
					</tr>
					<?php if ($profile) { ?>
						<tr>
							<th><?php echo Lang::txt('COM_COURSES_FIELD_NAME'); ?></th>
							<td><?php echo $this->escape(stripslashes($profile->get('name'))); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_COURSES_FIELD_USERNAME'); ?></th>
							<td><?php echo $this->escape(stripslashes($profile->get('username'))); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_COURSES_FIELD_EMAIL'); ?></th>
							<td><?php echo $this->escape(stripslashes($profile->get('email'))); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<script type="text/javascript">
		var offeringsections = new Array;
		<?php echo $js; ?>
	</script>

	<?php echo Html::input('token'); ?>
</form>
