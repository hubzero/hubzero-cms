<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('New Host'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOST_TYPES') . ': ' . $text, 'tools');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('hosttype');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<?php if ($this->getErrors()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_TOOLS_FIELD_NAME'); ?>:</label><br />
					<input type="text" name="fields[name]" id="field-name" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-value"><?php echo Lang::txt('COM_TOOLS_FIELD_VALUE'); ?>:</label><br />
					<input type="text" name="fields[value]" id="field-value" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->value)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_TOOLS_FIELD_DESCRIPTION'); ?>:</label><br />
					<input type="text" name="fields[description]" id="field-description" size="30" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->description)); ?>" />
				</div>
			</fieldset>
		</div>
		<div class="col span6">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_COL_BIT'); ?></th>
						<td><?php echo $this->escape($this->bit); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_COL_REFERENCES'); ?></th>
						<td><?php echo $this->escape($this->refs); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="fields[status]" value="<?php echo (isset($this->status)) ? $this->status : 'new'; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->name); ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>