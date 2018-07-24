<?php
// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = \Components\Fmns\Helpers\Permissions::getActions('fmn');

// Toolbar is a helper class to simplify the creation of Toolbar
// titles, buttons, spacers and dividers in the Admin Interface.
//
// Here we'll had the title of the component and options
// for saving based on if the user has permission to
// perform such actions. Everyone gets a cancel button.
$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FMNS') . ': ' . $text);
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
// Toolbar::help('fmn');

$this->css('fmns');

// Dynamic javascript (hence why in PHP form) to handle submitbutton events
$this->view('_edit_script')->display();
?>

<!--Setting enctype to multipart encoding allows us to access the image seperately with a request::getvar in partners -->
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form" enctype="multipart/form-data">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<!--to access values from database, need to use $this->escape($this->row->get('variable name in database')) -->

			<!--Name-->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FMNS_HINT_NAME'); ?>">
				<label for="field-name"><?php echo Lang::txt('COM_FMNS_FIELD_NAME'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[name]" id="field-name" size="35" value="<?php echo $this->escape($this->row->get('name')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_FMNS_HINT_NAME'); ?></span>
			</div>

			<!-- about text box-->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FMNS_FIELD_ABOUT'); ?>">
				<label for="field-about"><?php echo Lang::txt('COM_FMNS_FIELD_ABOUT'); ?></label>
				<?php echo $this->editor('fields[about]', $this->escape($this->row->about('raw')), 50, 15, 'field-about', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
				<span class="hint"><?php echo Lang::txt('COM_FMNS_HINT_ABOUT'); ?></span>
			</div>

			<!--GROUP CN -->
			<div class="input-wrap">
				<label for="fields-group_cn"><?php echo Lang::txt('COM_FMNS_FIELD_GROUP_CN'); ?></label>
				<select name="fields[group_cn]" id="fields-group_cn">
					<?php if ($this->row->get("group_cn") == '') { ?> <option value = "">select</option> <?php }?>
					<?php foreach ($this->grouprows as $val) { ?>
						<option<?php if ($this->row->get('group_cn') == $val->cn) { echo ' selected="selected"'; } ?> value="<?php echo $this->escape($val->cn); ?>"><?php echo $this->escape($val->cn); ?></option>
					<?php } ?>
				</select>
			</div>

			<!--Start date-->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FMNS_HINT_START_DATE'); ?>">
				<label for="field-start_date"><?php echo Lang::txt('COM_FMNS_FIELD_START_DATE'); ?></label><br />
				<input class = "input-wrap" type="date" name="fields[start_date]" id="field-start_date" size="45" value="<?php echo $this->escape($this->row->get('start_date')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_FMNS_HINT_START_DATE'); ?></span>
			</div>

			<!--End date-->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FMNS_HINT_STOP_DATE'); ?>">
				<label for="field-stop_date"><?php echo Lang::txt('COM_FMNS_FIELD_STOP_DATE'); ?></label><br />
				<input class = "input-wrap" type="date" name="fields[stop_date]" id="field-stop_date" size="45" value="<?php echo $this->escape($this->row->get('stop_date')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_FMNS_HINT_STOP_DATE'); ?></span>
			</div>
		</fieldset>
	</div>

	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_FMNS_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->get('id', 0); ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->get('id')); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_FMNS_FIELD_FMN_EVENT_ID'); ?>:</th>
					<td>
						<?php echo $fmn_event_id = $this->row->get('fmn_event_id', 0); ?>
						<input type="hidden" name="fields[fmn_event_id]" id="field-fmn_event_id" value="<?php echo $this->escape($this->row->get('fmn_event_id')); ?>" />
						<!--See com_resources/admin/views/items/tmpl/edit.php (and _edit_script.php)-->
						<?php if ($fmn_event_id == 0): ?>
							<input type="button" name="create_fmn_event" id="create_fmn_event" value="Create" onclick="submitbutton('createfmnevent');" />
						<?php else: ?>
							<input type="button" name="delete_fmn_event" id="delete_fmn_event" value="Delete" onclick="submitbutton('deletefmnevent');" />
							<input type="button" name="update_fmn_event" id="update_fmn_event" value="Update" onclick="submitbutton('updatefmnevent');" />
							<input type="button" name="edit_fmn_event" id="edit_fmn_event" value="Edit" onclick="submitbutton('editfmnevent');" />
						<?php endif;?>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- Publishing -->
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_FMNS_FIELD_STATE'); ?>:</label><br />
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>
			</div>
			<div class="input-wrap">
				<label for="field-featured"><?php echo Lang::txt('COM_FMNS_FIELD_FEATURED'); ?>:</label><br />
				<select name="fields[featured]" id="field-featured">
					<option value="0"<?php if ($this->row->get('featured') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JNO'); ?></option>
					<option value="1"<?php if ($this->row->get('featured') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JYES'); ?></option>
				</select>
			</div>
		</fieldset>

		<!-- Registration -->
		<fieldset id="regform" class="adminform">
			<legend><span><?php echo Lang::txt('COM_FMNS_FIELDSET_REGISTRATION'); ?></span></legend>

			<!-- Registration status -->
			<div class="input-wrap">
				<label for="field-reg_status"><?php echo Lang::txt('COM_FMNS_FIELD_STATUS'); ?>:</label><br />
				<select name="fields[reg_status]" id="field-reg_status">
					<option value="0"<?php if ($this->row->get('reg_status') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FMNS_FIELD_STATUS_CLOSED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_FMNS_FIELD_STATUS_OPEN'); ?></option>
				</select>
			</div>

			<!-- Due date -->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FMNS_HINT_REG_DUE_DATE'); ?>">
				<label for="field-reg_due_date"><?php echo Lang::txt('COM_FMNS_FIELD_REG_DUE_DATE'); ?></label><br />
				<input class = "input-wrap" type="date" name="fields[reg_due_date]" id="field-reg_due_date" size="45" value="<?php echo $this->escape($this->row->get('reg_due_date')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_FMNS_HINT_REG_DUE_DATE'); ?></span>
			</div>

			<hr>

			<!-- Registration event -->
			<table>
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_FMNS_FIELD_REG_EVENT_ID'); ?>:</th>
						<td>
							<?php echo $reg_event_id = $this->row->get('reg_event_id', 0); ?>
							<input type="hidden" name="fields[reg_event_id]" id="field-reg_event_id" value="<?php echo $this->escape($this->row->get('reg_event_id')); ?>" />
							<!--See com_resources/admin/views/items/tmpl/edit.php (and _edit_script.php)-->
							<?php if ($reg_event_id == 0): ?>
								<input type="button" name="create_reg_event" id="create_reg_event" value="Create" onclick="submitbutton('createregevent');" />
							<?php else: ?>
								<input type="button" name="delete_reg_event" id="delete_reg_event" value="Delete" onclick="submitbutton('deleteregevent');" />
								<input type="button" name="update_reg_event" id="update_reg_event" value="Update" onclick="submitbutton('updateregevent');" />
								<input type="button" name="edit_reg_event" id="edit_reg_event" value="Edit" onclick="submitbutton('editregevent');" />
							<?php endif;?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
