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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Wiki\Helpers\Permissions::getActions('comment');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_WIKI') . ': ' . Lang::txt('COM_WIKI_PAGE') . ': ' . Lang::txt('COM_WIKI_COMMENTS') . ': ' . $text, 'wiki');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
	Toolbar::apply();
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

	// do field validation
	if ($('#field-ctext').value == ''){
		alert(<?php echo Lang::txt('COM_WIKI_ERROR_MISSING_COMMENT'); ?>);
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<div class="grid">
		<div class="colspan7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1"<?php if ($this->row->get('anonymous')) { echo ' checked="checked"'; } ?> />
					<label for="field-anonymous"><?php echo Lang::txt('COM_WIKI_FIELD_ANONYMOUS'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-ctext"><?php echo Lang::txt('COM_WIKI_FIELD_CONTENT'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<textarea name="fields[ctext]" id="field-ctext" cols="35" rows="15"><?php echo $this->escape(stripslashes($this->row->get('ctext'))); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							echo $this->escape($this->row->creator->get('name'));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo $this->row->get('created'); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->get('created')); ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_WIKI_FIELD_PAGE'); ?>:</th>
						<td>
							<?php echo $this->row->get('page_id'); ?>
							<input type="hidden" name="fields[page_id]" id="field-page_id" value="<?php echo $this->escape($this->row->get('page_id')); ?>" />
							<input type="hidden" name="pageid" value="<?php echo $this->escape($this->row->get('page_id')); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="fields[parent]" value="<?php echo $this->escape($this->row->get('parent')); ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
