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

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS') . ': ' . $text, 'tools.png');
Toolbar::apply();
Toolbar::save();
Toolbar::spacer();
Toolbar::cancel();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-alias"><?php echo Lang::txt('COM_TOOLS_FIELD_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_TOOLS_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-path"><?php echo Lang::txt('COM_TOOLS_FIELD_UUID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[path]" id="field-path" value="<?php echo $this->escape(stripslashes($this->row->get('path'))); ?>" />
			</div>
			<div class="input-wrap">
				<label for="field-introtext"><?php echo Lang::txt('COM_TOOLS_FIELD_DESCRIPTION'); ?>:</label>
				<textarea name="fields[introtext]" id="field-introtext" cols="35" rows="5"><?php echo $this->escape($this->row->get('introtext')); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_TOOLS_FIELD_ID'); ?></th>
					<td><?php echo $this->row->get('id', 0); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_TOOLS_FIELD_STATUS'); ?></th>
					<td><?php echo $this->row->get('status'); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="fields[type]" value="<?php echo $this->row->get('type'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo Html::input('token'); ?>
</form>