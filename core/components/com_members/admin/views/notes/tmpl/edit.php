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

$checkedOut = !($this->row->get('checked_out') == 0 || $this->row->get('checked_out') == User::get('id'));
$canDo = Components\Members\Helpers\Admin::getActions($this->row->get('category_id'), $this->row->get('id'));

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_NOTES') . ': ' . $text, 'user');

// If not checked out, can save the item.
if (!$checkedOut && ($canDo->get('core.edit') || (count(User::getAuthorisedCategories('com_users', 'core.create')))))
{
	Toolbar::apply('note.apply');
	Toolbar::save('note.save');
}

if (!$checkedOut && (count(User::getAuthorisedCategories('com_users', 'core.create'))))
{
	Toolbar::save2new('note.save2new');
}

// If an existing item, can save to a copy.
if (!$this->row->isNew() && (count(User::getAuthorisedCategories('com_users', 'core.create')) > 0))
{
	Toolbar::save2copy('note.save2copy');
}
Toolbar::cancel('note.cancel');
Toolbar::divider();
Toolbar::help('note');

Html::behavior('tooltip');
Html::behavior('formvalidation');
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
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-subject"><?php echo Lang::txt('COM_MEMBERS_FIELD_SUBJECT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[subject]" id="field-subject" value="<?php echo $this->escape(stripslashes($this->row->get('subject'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-body"><?php echo Lang::txt('COM_MEMBERS_FIELD_BODY'); ?>:</label>
					<?php echo $this->editor('fields[body]', $this->escape($this->row->get('body')), 50, 15, 'field-body', array('class' => 'minimal no-footer')); ?>
				</div>

				<div class="input-wrap">
					<label for="field-category_id"><?php echo Lang::txt('COM_MEMBERS_FIELD_CATEGORY'); ?>:</label>
					<select name="fields[catid]" id="field-category_id">
						<option value="0"><?php echo Lang::txt('JOPTION_SELECT_CATEGORY');?></option>
						<?php echo Html::select('options', Html::category('options', 'com_users.notes'), 'value', 'text', $this->row->get('category_id')); ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-category_id"><?php echo Lang::txt('COM_MEMBERS_FIELD_USER'); ?>:</label>
					<?php echo Components\Members\Helpers\Admin::getUserInput('fields[user_id]', 'fielduser_id', $this->row->get('user_id')); ?>
				</div>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_MEMBERS_FIELD_STATE'); ?>:</label>
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
					</select>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_FIELD_REVIEW_TIME_DESC'); ?>">
					<label for="field-review_time"><?php echo Lang::txt('COM_MEMBERS_FIELD_REVIEW_TIME_LABEL'); ?>:</label>
					<?php echo Html::input('calendar', 'fields[review_time]', ($this->row->get('review_time') != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->get('review_time'))->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-review_time')); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_FIELD_ID'); ?></th>
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
