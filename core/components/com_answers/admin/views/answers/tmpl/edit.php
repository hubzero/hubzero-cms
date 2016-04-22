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

$canDo = Components\Answers\Helpers\Permissions::getActions('answer');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_ANSWERS_TITLE') . ': ' . Lang::txt('COM_ANSWERS_QUESTIONS') . ': ' . $text, 'answers');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('response');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	if (pressbutton =='resethelpful') {
		if (confirm('<?php echo Lang::txt("COM_ANSWERS_CONFIRM_RESET"); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if (document.getElementById('field-answer').value == ''){
		alert('<?php echo Lang::txt('COM_ANSWERS_ERROR_MISSING_ANSWER'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<input type="checkbox" name="answer[anonymous]" id="field-anonymous" value="1" <?php echo ($this->row->get('anonymous')) ? 'checked="checked"' : ''; ?> />
					<label for="field-anonymous"><?php echo Lang::txt('COM_ANSWERS_FIELD_ANONYMOUS'); ?></label>
				</div>
				<div class="input-wrap">
					<label for="field-question"><?php echo Lang::txt('COM_ANSWERS_FIELD_QUESTION'); ?></label><br />
					<input type="text" id="field-question" disabled="disabled" readonly="readonly" value="<?php echo $this->escape(strip_tags($this->question->get('subject'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-answer"><?php echo Lang::txt('COM_ANSWERS_FIELD_ANSWER'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<?php echo $this->editor('answer[answer]', $this->escape($this->row->get('answer')), 50, 15, 'field-answer', array('class' => 'minimal no-footer')); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_ANSWERS_FIELD_ID'); ?>:</th>
						<td><?php echo $this->row->get('id'); ?></td>
					</tr>
				<?php if ($this->row->get('id')) { ?>
					<tr>
						<th><?php echo Lang::txt('COM_ANSWERS_FIELD_CREATED'); ?>:</th>
						<td><?php echo $this->row->get('created'); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_ANSWERS_FIELD_CREATOR'); ?>:</th>
						<td><?php echo $this->escape(stripslashes($this->row->creator->get('name'))); ?></td>
					</tr>
				<?php } ?>
					<tr>
						<th><?php echo Lang::txt('COM_ANSWERS_FIELD_HELPFUL'); ?>:</th>
						<td>
							<span class="votes up">+<?php echo $this->row->get('helpful'); ?></span>
							<span class="votes down">-<?php echo $this->row->get('nothelpful'); ?></span>
							<?php if ($this->row->get('helpful') > 0 || $this->row->get('nothelpful') > 0) { ?>
								<input type="button" name="reset_helpful" value="<?php echo Lang::txt('COM_ANSWERS_FIELD_RESET'); ?>" onclick="submitbutton('reset');" />
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_ANSWERS_FIELD_ACCEPT'); ?></label><br />
					<input type="checkbox" name="answer[state]" id="field-state" value="1" <?php echo $this->row->get('state') ? 'checked="checked"' : ''; ?> /> (<?php echo ($this->row->get('state') == 1) ? Lang::txt('COM_ANSWERS_STATE_ACCEPTED') : Lang::txt('COM_ANSWERS_STATE_UNACCEPTED'); ?>)
				</div>

				<div class="input-wrap">
					<label for="field-created_by"><?php echo Lang::txt('COM_ANSWERS_FIELD_CREATOR'); ?></label><br />
					<input type="text" name="answer[created_by]" id="field-created_by" size="25" maxlength="50" value="<?php echo $this->escape($this->row->get('created_by', User::get('id'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-created"><?php echo Lang::txt('COM_ANSWERS_FIELD_CREATED'); ?></label><br />
					<?php echo Html::input('calendar', 'answer[created]', $this->row->get('created', Date::toSql()), array('id' => 'field-created')); ?>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="answer[question_id]" value="<?php echo $this->question->get('id'); ?>" />
	<input type="hidden" name="answer[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
