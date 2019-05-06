<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$checkedOut = !($this->row->get('checked_out') == 0 || $this->row->get('checked_out') == User::get('id'));
$canDo = Components\Members\Helpers\Admin::getActions($this->row->get('category_id'), $this->row->get('id'));

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_NOTES') . ': ' . $text, 'user');

// If not checked out, can save the item.
if (!$checkedOut && ($canDo->get('core.edit') || (count(User::getAuthorisedCategories('com_members', 'core.create')))))
{
	Toolbar::apply();
	Toolbar::save();
}

if (!$checkedOut && (count(User::getAuthorisedCategories('com_members', 'core.create'))))
{
	Toolbar::save2new();
}

// If an existing item, can save to a copy.
if (!$this->row->isNew() && (count(User::getAuthorisedCategories('com_members', 'core.create')) > 0))
{
	Toolbar::save2copy();
}
Toolbar::cancel();
Toolbar::divider();
Toolbar::help('note');

Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-subject"><?php echo Lang::txt('COM_MEMBERS_FIELD_SUBJECT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[subject]" id="field-subject" class="required" value="<?php echo $this->escape(stripslashes($this->row->get('subject'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-body"><?php echo Lang::txt('COM_MEMBERS_FIELD_BODY'); ?>:</label>
					<?php echo $this->editor('fields[body]', $this->escape($this->row->get('body')), 50, 15, 'field-body', array('class' => 'minimal no-footer')); ?>
				</div>

				<div class="input-wrap">
					<label for="field-category_id"><?php echo Lang::txt('COM_MEMBERS_FIELD_CATEGORY'); ?>:</label>
					<select name="fields[catid]" id="field-category_id">
						<option value="0"><?php echo Lang::txt('JOPTION_SELECT_CATEGORY');?></option>
						<?php echo Html::select('options', Html::category('options', 'com_members'), 'value', 'text', $this->row->get('category_id')); ?>
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
					<?php echo Html::input('calendar', 'fields[review_time]', ($this->row->get('review_time') && $this->row->get('review_time') != '0000-00-00 00:00:00' ? $this->escape(Date::of($this->row->get('review_time'))->toLocal('Y-m-d H:i:s')) : ''), array('id' => 'field-review_time')); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_FIELD_ID'); ?></th>
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
