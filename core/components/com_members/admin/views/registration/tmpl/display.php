<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Members\Helpers\Admin::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS_REGISTRATION'), 'users');
if ($canDo->get('core.edit'))
{
	Toolbar::preferences($this->option);
	Toolbar::save();
	Toolbar::cancel();
}

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<fieldset>
		<table class="adminlist">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_AREA'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_CREATE_ACCOUNT'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_PROXY_CREATE_ACCOUNT'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_UPDATE_ACCOUNT'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_EDIT_ACCOUNT'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($this->params as $field => $values)
{
	if (substr($field, 0, strlen('registration')) == 'registration')
	{
		$title = $values->title;
		$value = $values->value;

		$create = strtoupper(substr($value, 0, 1));
		$proxy  = strtoupper(substr($value, 1, 1));
		$update = strtoupper(substr($value, 2, 1));
		$edit   = strtoupper(substr($value, 3, 1));

		$field = str_replace('registration', '', $values->name);
?>
				<tr>
					<td><?php echo $title; ?></td>
					<td>
						<?php if ($create != '-') : ?>
							<select name="settings[<?php echo $field; ?>][create]">
								<option value="O"<?php if ($create == 'O') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'); ?></option>
								<option value="R"<?php if ($create == 'R') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'); ?></option>
								<option value="H"<?php if ($create == 'H') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'); ?></option>
								<option value="U"<?php if ($create == 'U') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'); ?></option>
							</select>
						<?php else: ?>
							<?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
							<input type="hidden" name="settings[<?php echo $field; ?>][create]" value="-">
						<?php endif; ?>
					</td>
					<td>
						<?php if ($proxy != '-') : ?>
							<select name="settings[<?php echo $field; ?>][proxy]">
								<option value="O"<?php if ($proxy == 'O') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'); ?></option>
								<option value="R"<?php if ($proxy == 'R') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'); ?></option>
								<option value="H"<?php if ($proxy == 'H') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'); ?></option>
								<option value="U"<?php if ($proxy == 'U') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'); ?></option>
							</select>
						<?php else: ?>
							<?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
							<input type="hidden" name="settings[<?php echo $field; ?>][proxy]" value="-">
						<?php endif; ?>
					</td>
					<td>
						<?php if ($update != '-') : ?>
							<select name="settings[<?php echo $field; ?>][update]">
								<option value="O"<?php if ($update == 'O') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'); ?></option>
								<option value="R"<?php if ($update == 'R') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'); ?></option>
								<option value="H"<?php if ($update == 'H') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'); ?></option>
								<option value="U"<?php if ($update == 'U') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'); ?></option>
							</select>
						<?php else: ?>
							<?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
							<input type="hidden" name="settings[<?php echo $field; ?>][update]" value="-">
						<?php endif; ?>
					</td>
					<td>
						<?php if ($edit != '-') : ?>
							<select name="settings[<?php echo $field; ?>][edit]">
								<option value="O"<?php if ($edit == 'O') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL'); ?></option>
								<option value="R"<?php if ($edit == 'R') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED'); ?></option>
								<option value="H"<?php if ($edit == 'H') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE'); ?></option>
								<option value="U"<?php if ($edit == 'U') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY'); ?></option>
							</select>
						<?php else: ?>
							<?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
							<input type="hidden" name="settings[<?php echo $field; ?>][edit]" value="-">
						<?php endif; ?>
					</td>
				</tr>
<?php
	}
}
?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />

		<?php echo Html::input('token'); ?>
	</fieldset>
</form>