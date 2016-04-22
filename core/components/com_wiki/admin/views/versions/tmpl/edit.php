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

$canDo = Components\Wiki\Helpers\Permissions::getActions('page');

$text = ($this->task == 'editrevision' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_WIKI') . ': ' . Lang::txt('COM_WIKI_REVISION') . ': ' . $text, 'wiki');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
	Toolbar::apply();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('revision');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-summary"><?php echo Lang::txt('COM_WIKI_FIELD_EDIT_SUMMARY'); ?>:</label><br />
					<input type="text" name="revision[summary]" id="field-summary" size="55" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('summary'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-pagetext"><?php echo Lang::txt('COM_WIKI_FIELD_TEXT'); ?>:</label><br />
					<textarea name="revision[pagetext]" id="field-pagetext" cols="50" rows="40"><?php echo $this->escape(stripslashes($this->row->get('pagetext'))); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_PAGE') . ' ' . Lang::txt('COM_WIKI_FIELD_TITLE'); ?>:</th>
						<td><?php echo $this->escape(stripslashes($this->page->get('title'))); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_PAGE') . ' ' . Lang::txt('COM_WIKI_FIELD_PAGENAME'); ?>:</th>
						<td><?php echo $this->escape(stripslashes($this->page->get('pagename'))); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_PAGE') . ' ' . Lang::txt('COM_WIKI_FIELD_SCOPE'); ?>:</th>
						<td><?php echo $this->escape(stripslashes($this->page->get('scope') . ':' . $this->page->get('scope_id'))); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_PAGE') . ' ' . Lang::txt('COM_WIKI_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->get('page_id')); ?>
							<input type="hidden" name="revision[page_id]" id="revision-page_id" value="<?php echo $this->escape($this->row->get('page_id')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->get('id')); ?>
							<input type="hidden" name="revision[id]" id="revision-id" value="<?php echo $this->escape($this->row->get('id')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_FIELD_REVISION'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->get('version')); ?>
							<input type="hidden" name="revision[version]" id="revision-version" value="<?php echo $this->escape($this->row->get('version')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN'))); ?>
							<input type="hidden" name="revision[created_by]" id="revision-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo $this->row->get('created'); ?>
							<input type="hidden" name="revision[created]" id="revision-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_WIKI_FIELDSET_PARAMETERS'); ?></span></legend>

				<div class="input-wrap">
					<input type="checkbox" name="revision[minor_edit]" id="field-minor_edit" value="1" <?php echo $this->row->get('minor_edit') ? 'checked="checked"' : ''; ?> />
					<label for="field-minor_edit"><?php echo Lang::txt('COM_WIKI_FIELD_MINOR_EDIT'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-approved"><?php echo Lang::txt('COM_WIKI_FIELD_STATE'); ?>:</label><br />
					<select name="revision[approved]" id="field-approved">
						<option value="0"<?php echo $this->row->get('approved') == 0 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WIKI_STATE_NOT_APPROVED'); ?></option>
						<option value="1"<?php echo $this->row->get('approved') == 1 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WIKI_STATE_APPROVED'); ?></option>
						<option value="2"<?php echo $this->row->get('approved') == 2 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WIKI_STATE_TRASHED'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="pageid" value="<?php echo $this->row->get('page_id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>