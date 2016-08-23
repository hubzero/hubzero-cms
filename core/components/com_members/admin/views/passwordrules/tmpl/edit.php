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

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_PASSWORD_RULES') . ': '. $text, 'user');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
	Toolbar::cancel();
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

	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES'); ?></span></legend>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />

				<div class="input-wrap">
					<label for="field-rule"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_RULE'); ?>:</label>
					<?php echo $this->rules_list; ?>
				</div>
				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_DESCRIPTION'); ?>:</label>
					<input type="text" name="fields[description]" id="field-description" value="<?php echo $this->escape(stripslashes($this->row->get('description'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-failuremsg"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_FAILURE_MESSAGE'); ?>:</label>
					<input type="text" name="fields[failuremsg]" id="field-failuremsg" value="<?php echo $this->escape(stripslashes($this->row->get('failuremsg'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-value"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_VALUE'); ?>:</label>
					<input type="text" name="fields[value]" id="field-value" value="<?php echo $this->escape(stripslashes($this->row->get('value'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-group"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_GROUP'); ?>:</label>
					<input type="text" name="fields[grp]" id="field-group" value="<?php echo $this->escape(stripslashes($this->row->get('grp'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-class"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES_CLASS'); ?>:</label>
					<input type="text" name="fields[class]" id="field-class" value="<?php echo $this->escape(stripslashes($this->row->get('class'))); ?>" />
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_PASSWORD_ID'); ?></th>
						<td><?php echo $this->row->get('id'); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>