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
if (!$this->tmpl)
{
	Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ASSETS') . ': ' . $text, 'courses.png');
	if ($canDo->get('core.edit'))
	{
		Toolbar::save();
	}
	Toolbar::cancel();
}

Html::behavior('framework', true);

if ($this->row->get('id'))
{
	$id = $this->row->get('id');
}
else
{
	$id = 'tmp' . time() . rand(0, 10000);
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
	if ($('#field-title').val() == '') {
		alert('<?php echo Lang::txt('COM_COURSES_ERROR_MISSING_TITLE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
function saveAndUpdate()
{
	submitbutton('save');
	window.top.setTimeout(function(){
		var src = window.parent.document.getElementById('assets').src;
		window.parent.document.getElementById('assets').src = src;

		window.parent.document.assetform.close();
	}, 700);
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="<?php echo ($this->tmpl == 'component') ? 'component-form' : 'item-form'; ?>" enctype="multipart/form-data">
<?php if ($this->tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" onclick="saveAndUpdate();"><?php echo Lang::txt('COM_COURSES_SAVE'); ?></button>
				<button type="button" onclick="window.parent.document.assetform.close();"><?php echo Lang::txt('COM_COURSES_CANCEL'); ?></button>
			</div>
			<?php echo $text; ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
			<input type="hidden" name="fields[course_id]" value="<?php echo $this->escape($this->course_id); ?>" />
			<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->scope); ?>" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->scope_id); ?>" />

			<input type="hidden" name="fields[lid]" value="<?php echo $this->escape($id); ?>" />

			<input type="hidden" name="tmpl" value="<?php echo $this->escape($this->tmpl); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>">
			<input type="hidden" name="task" value="save" />

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="field-type"><?php echo Lang::txt('COM_COURSES_FIELD_TYPE'); ?>:</label>
					<select name="fields[type]" id="field-type">
						<option value="video"<?php if ($this->row->get('type') == 'video') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_VIDEO'); ?></option>
						<option value="file"<?php if ($this->row->get('type') == 'file') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_FILE'); ?></option>
						<option value="form"<?php if ($this->row->get('type') == 'form') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_FORM'); ?></option>
						<option value="text"<?php if ($this->row->get('type') == 'text') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_TEXT'); ?></option>
						<option value="url"<?php if ($this->row->get('type') == 'url') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_URL'); ?></option>
					</select>
				</div>
			</div>
			<div class="col width-50 fltrt">
				<div class="input-wrap">
					<label for="field-subtype"><?php echo Lang::txt('COM_COURSES_FIELD_SUBTYPE'); ?>:</label>
					<select name="fields[subtype]" id="field-subtype">
						<option value="video"<?php if ($this->row->get('subtype') == 'video') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_VIDEO'); ?></option>
						<option value="embedded"<?php if ($this->row->get('subtype') == 'embedded') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_EMBEDDED'); ?></option>
						<option value="file"<?php if ($this->row->get('subtype') == 'file') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_FILE'); ?></option>
						<option value="exam"<?php if ($this->row->get('subtype') == 'exam') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_EXAM'); ?></option>
						<option value="quiz"<?php if ($this->row->get('subtype') == 'quiz') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_QUIZ'); ?></option>
						<option value="homework"<?php if ($this->row->get('subtype') == 'homework') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_HOMEWORK'); ?></option>
						<option value="note"<?php if ($this->row->get('subtype') == 'note') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_NOTE'); ?></option>
						<option value="wiki"<?php if ($this->row->get('subtype') == 'wiki') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_ASSET_TYPE_WIKI'); ?></option>
					</select>
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label>
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_UNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_PUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COURSES_TRASHED'); ?></option>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_COURSES_FIELD_TITLE'); ?>:</label>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-url"><?php echo Lang::txt('COM_COURSES_FIELD_URL'); ?>:</label>
				<input type="text" name="fields[url]" id="field-url" value="<?php echo $this->escape(stripslashes($this->row->get('url'))); ?>" size="50" />
			</div>
			<div class="input-wrap">
				<label for="field-content"><?php echo Lang::txt('COM_COURSES_FIELD_CONTENT'); ?>:</label>
				<textarea name="fields[content]" id="field-content" rows="4" cols="35"><?php echo $this->escape(stripslashes($this->row->get('content'))); ?></textarea>
			</div>

			<iframe width="100%" height="225" name="filelist" id="filelist" frameborder="0" src="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=media&tmpl=component&listdir=' . $id . '&course=' . $this->escape($this->course_id)); ?>"></iframe>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo Html::input('token'); ?>
</form>
