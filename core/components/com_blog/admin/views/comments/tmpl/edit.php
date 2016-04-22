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

$canDo = Components\Blog\Admin\Helpers\Permissions::getActions('entry');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_BLOG_TITLE') . ': ' . Lang::txt('COM_BLOG_COL_COMMENTS') . ': ' . $text, 'blog');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('comment');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if ($('#field-content').val() == ''){
		alert("<?php echo Lang::txt('COM_BLOG_ERROR_MISSING_CONTENT'); ?>");
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_BLOG_FIELD_ANONYMOUS_HINT'); ?>">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->row->get('anonymous')) { echo ' checked="checked"'; } ?> />
					<label for="field-anonymous"><?php echo Lang::txt('COM_BLOG_FIELD_ANONYMOUS'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-content"><?php echo Lang::txt('COM_BLOG_FIELD_CONTENT'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo $this->editor('fields[content]', $this->escape($this->row->get('content')), 50, 15, 'field-content', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_BLOG_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id', 0); ?>
							<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
							<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_BLOG_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							$editor = User::getInstance($this->row->get('created_by'));
							echo $this->escape($editor->get('name'));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_BLOG_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo $this->row->get('created'); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_BLOG_FIELD_ENTRY'); ?>:</th>
						<td>
							<?php echo $this->row->get('entry_id'); ?>
							<input type="hidden" name="fields[entry_id]" id="field-entry_id" value="<?php echo $this->escape($this->row->get('entry_id')); ?>" />
							<input type="hidden" name="entry_id" value="<?php echo $this->escape($this->row->get('entry_id')); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_BLOG_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
						<option value="3"<?php if ($this->row->get('state') == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_BLOG_FIELD_STATE_FLAGGED'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="fields[parent]" value="<?php echo $this->row->get('parent'); ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>