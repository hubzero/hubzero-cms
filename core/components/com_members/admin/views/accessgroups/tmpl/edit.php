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

$canDo = Components\Members\Helpers\Admin::getActions('component');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_ACCESSGROUPS') . ': ' . $text, 'user');
if ($canDo->get('core.edit')||$canDo->get('core.create'))
{
	Toolbar::apply();
	Toolbar::save();
}
if ($canDo->get('core.create'))
{
	Toolbar::save2new();
}
// If an existing item, can save to a copy.
if (!$this->row->get('id') && $canDo->get('core.create'))
{
	Toolbar::save2copy();
}
Toolbar::cancel();
Toolbar::divider();
Toolbar::help('group');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_GROUP_FIELD_TITLE_DESC'); ?>">
					<label for="field-title"><?php echo Lang::txt('COM_MEMBERS_GROUP_FIELD_TITLE_LABELE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape($this->row->get('title')); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_GROUP_FIELD_PARENT_DESC'); ?>">
					<label for="field-parent"><?php echo Lang::txt('COM_MEMBERS_GROUP_FIELD_PARENT_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<select name="fields[parent]" id="field-parent">
						<?php foreach ($this->options as $option): ?>
							<option value="<?php echo $option->get('id'); ?>"<?php if ($option->get('id') == $this->row->get('parent')) { echo ' selected="selected"'; } ?>><?php echo str_repeat('- ', $option->get('level', 1)) . $option->get('title'); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_MEMBERS_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id'); ?>
							<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
