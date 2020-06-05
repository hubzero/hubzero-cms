<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Forum\Helpers\Permissions::getActions('section');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_SECTIONS') . ': ' . $text, 'forum');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('section');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-scope"><?php echo Lang::txt('COM_FORUM_FIELD_SCOPE'); ?>:</label><br />
							<input type="text" name="fields[scope]" id="field-scope" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('scope'))); ?>" />
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-scope_id"><?php echo Lang::txt('COM_FORUM_FIELD_SCOPE_ID'); ?>:</label><br />
							<input type="text" name="fields[scope_id]" id="field-scope_id" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('scope_id'))); ?>" />
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="required" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FORUM_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_FORUM_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_FORUM_FIELD_ALIAS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_FORUM_FIELD_CREATED'); ?>:</th>
						<td>
							<?php echo $this->escape($this->row->creator->get('name')); ?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->row->get('created_by'); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_FORUM_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php echo Date::of($this->row->get('created'))->toLocal(); ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->row->get('created'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_FORUM_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php echo ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php echo ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="2"<?php echo ($this->row->get('state') == 2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
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
		<div class="col span12">
			<fieldset class="panelform">
				<legend><span><?php echo Lang::txt('COM_FORUM_FIELDSET_RULES'); ?></span></legend>
				<?php echo $this->form->getLabel('rules'); ?>
				<?php echo $this->form->getInput('rules'); ?>
			</fieldset>
		</div>
	<?php endif; ?>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
