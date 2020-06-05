<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS').': '. $text, 'tools');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('tool');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_TOOLS_FIELD_TITLE'); ?>:</label><br />
					<input type="text" name="fields[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-ticketid"><?php echo Lang::txt('COM_TOOLS_FIELD_TICKETID'); ?>:</label><br />
					<input type="text" name="fields[ticketid]" id="field-ticketid" value="<?php echo $this->escape($this->row->ticketid); ?>" />
				</div>

				<?php /*<div class="input-wrap">
					<label for="field-toolaccess"><?php echo Lang::txt('COM_TOOLS_FIELD_TOOLACCESS'); ?>:</label><br />
					<input type="text" name="fields[toolaccess]" id="field-toolaccess" value="<?php echo $this->escape($this->row->toolaccess); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-codeaccess"><?php echo Lang::txt('COM_TOOLS_FIELD_CODEACCESS'); ?>:</label><br />
					<input type="text" name="fields[codeaccess]" id="field-codeaccess" value="<?php echo $this->escape($this->row->codeaccess); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-wikiaccess"><?php echo Lang::txt('COM_TOOLS_FIELD_WIKIACCESS'); ?>:</label><br />
					<input type="text" name="fields[wikiaccess]" id="field-wikiaccess" value="<?php echo $this->escape($this->row->wikiaccess); ?>" />
				</div>*/?>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_TOOLS_FIELD_STATE'); ?>:</label><br />
					<select name="fields[state]" id="field-state">
						<option value="0"<?php if ($this->row->state == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
						<option value="1"<?php if ($this->row->state == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_REGISTERED'); ?></option>
						<option value="2"<?php if ($this->row->state == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_CREATED'); ?></option>
						<option value="3"<?php if ($this->row->state == 3) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_UPLOADED'); ?></option>
						<option value="4"<?php if ($this->row->state == 4) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_INSTALLED'); ?></option>
						<option value="5"<?php if ($this->row->state == 5) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_UPDATED'); ?></option>
						<option value="6"<?php if ($this->row->state == 6) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_APPROVED'); ?></option>
						<option value="7"<?php if ($this->row->state == 7) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
						<option value="8"<?php if ($this->row->state == 8) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_RETIRED'); ?></option>
						<option value="9"<?php if ($this->row->state == 9) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_ABANDONED'); ?></option>
					</select>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_FIELD_ID'); ?>:</th>
						<td><?php echo $this->escape($this->row->id);?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_FIELD_NAME'); ?>:</th>
						<td><?php echo $this->escape(stripslashes($this->row->toolname)); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_FIELD_REGISTERED'); ?>:</th>
						<td><?php echo $this->escape($this->row->registered); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_TOOLS_FIELD_REGISTERED_BY'); ?>:</th>
						<td><?php echo $this->escape($this->row->registered_by); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>