<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Support\Helpers\Permissions::getActions('status');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_STATUS') . ': ' . $text, 'support');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('status');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->css('jquery.colpick.css', 'system')
	->js('jquery.colpick.js', 'system')
	->js('edit.js');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_SUPPORT_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-open"><?php echo Lang::txt('COM_SUPPORT_FIELD_FOR'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
							<select name="fields[open]" id="field-open">
								<option value="1"<?php if ($this->row->get('open') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_FIELD_FOR_OPEN'); ?></option>
								<option value="0"<?php if ($this->row->get('open') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_FIELD_FOR_CLOSED'); ?></option>
							</select>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="field-color"><?php echo Lang::txt('Color'); ?>:</label>
							<input type="text" name="fields[color]" id="field-color" value="<?php echo $this->escape($this->row->get('color')); ?>" />
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_SUPPORT_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id'); ?>
							<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
