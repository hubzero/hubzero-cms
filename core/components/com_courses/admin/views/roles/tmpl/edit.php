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

$canDo = \Components\Courses\Helpers\Permissions::getActions();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ROLES') . ': ' . $text, 'courses.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	var field = document.getElementById('field-title');
	if (field.value == '') {
		alert('<?php echo Lang::txt('COM_COURSES_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm">
	<div class="col width-70 fltlft">
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
				<input type="text" name="fields[title]" id="field-title" size="50" value="<?php echo $this->escape($this->row->title); ?>" />
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
	<div class="col width-30 fltrt">
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
	<div class="clr"></div>

	<?php echo Html::input('token'); ?>
</form>