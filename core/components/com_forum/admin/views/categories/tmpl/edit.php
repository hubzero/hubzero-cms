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

$canDo = Components\Forum\Helpers\Permissions::getActions('section');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_CATEGORIES') . ': ' . $text, 'forum');
Toolbar::spacer();
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');

$create_date = NULL;
if (intval($this->row->created) <> 0)
{
	$create_date = Date::of($this->row->created)->toSql();
}
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert( '<?php echo Lang::txt('COM_FORUM_ERROR_MISSING_TITLE'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-scope]"><?php echo Lang::txt('COM_FORUM_FIELD_SCOPE'); ?>:</label><br />
							<input type="text" name="fields[scope]" id="field-scope]" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->scope)); ?>" />
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-scope_id"><?php echo Lang::txt('COM_FORUM_FIELD_SCOPE_ID'); ?>:</label><br />
							<input type="text" name="fields[scope_id]" id="field-scope_id" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->scope_id)); ?>" />
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<label for="field-section_id"><?php echo Lang::txt('COM_FORUM_FIELD_SECTION'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<select name="fields[section_id]" id="field-section_id">
						<option value="-1"><?php echo Lang::txt('COM_FORUM_FIELD_SECTION_SELECT'); ?></option>
						<?php foreach ($this->sections as $group => $sections) { ?>
							<optgroup label="<?php echo $this->escape(stripslashes($group)); ?>">
								<?php foreach ($sections as $section) { ?>
									<option value="<?php echo $section->id; ?>"<?php if ($this->row->section_id == $section->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->title)); ?></option>
								<?php } ?>
							</optgroup>
						<?php } ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FORUM_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_FORUM_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->alias)); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_FORUM_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_FORUM_FIELD_DESCRIPTION'); ?></label><br />
					<textarea name="fields[description]" id="field-description" cols="35" rows="5"><?php echo $this->escape(stripslashes($this->row->description)); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_FORUM_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							echo $this->escape($this->row->creator->get('name'));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->row->created_by; ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_FORUM_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo $this->row->created; ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->created; ?>" />
						</td>
					</tr>
				<?php if ($this->row->modified_by) { ?>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_FORUM_FIELD_MODIFIER'); ?>:</th>
						<td>
							<?php
							echo $this->escape($this->row->modifier->get('name'));
							?>
							<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->row->modified_by; ?>" />
						</td>
					</tr>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_FORUM_FIELD_MODIFIED'); ?>:</th>
						<td>
							<?php echo $this->row->modified; ?>
							<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->row->modified; ?>" />
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<input class="option" type="checkbox" name="fields[closed]" id="field-closed" value="1"<?php if ($this->row->closed) { echo ' checked="checked"'; } ?> />
					<label for="field-closed"><?php echo Lang::txt('COM_FORUM_FIELD_CLOSED'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_FORUM_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php echo ($this->row->state == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php echo ($this->row->state == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php echo ($this->row->state == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-access"><?php echo Lang::txt('COM_FORUM_FIELD_ACCESS'); ?>:</label><br />
					<select name="fields[access]" id="field-access">
						<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->row->get('access')); ?>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<?php if ($canDo->get('core.admin')): ?>
		<div class="col width-100">
			<fieldset class="panelform">
				<legend><span><?php echo Lang::txt('COM_FORUM_FIELDSET_RULES'); ?></span></legend>
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
	<?php endif; ?>

	<input type="hidden" name="fields[scope]" value="<?php echo $this->row->scope; ?>" />
	<input type="hidden" name="fields[scope_id]" value="<?php echo $this->row->scope_id; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
