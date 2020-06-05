<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getCmd('tmpl', '');

$text = ($this->task == 'edit' ? Lang::txt('COM_GROUPS_EDIT') : Lang::txt('COM_GROUPS_NEW'));

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_GROUPS').': ' . $text, 'groups');
	if ($canDo->get('core.edit'))
	{
		Toolbar::save();
	}
	Toolbar::cancel();
}

Html::behavior('framework');

$this->js('membership.js');
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="component-form" data-redirect="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->get('cn')); ?>" data-invalid-msg="<?php echo Lang::txt('COM_GROUPS_ERROR_MISSING_INFORMATION'); ?>">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration" >
			<div class="fltrt configuration-options">
				<button type="button" id="btn-save"><?php echo Lang::txt( 'COM_GROUPS_MEMBER_SAVE' );?></button>
				<button type="button" id="btn-cancel"><?php echo Lang::txt( 'COM_GROUPS_MEMBER_CANCEL' );?></button>
			</div>
			<?php echo Lang::txt('COM_GROUPS_MEMBER_ADD') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col span12">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_GROUPS_DETAILS'); ?></span></legend>

			<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="no_html" value="<?php echo ($tmpl == 'component') ? '1' : '0'; ?>">
			<input type="hidden" name="task" value="addusers" />

			<table class="admintable">
				<tbody>
					<tr>
						<th><label for="field-usernames"><?php echo Lang::txt('COM_GROUPS_ADD_USERNAME'); ?>:</label></th>
						<td><input type="text" name="usernames" class="input-username" id="field-usernames" value="" size="50" /></td>
					</tr>
					<tr>
						<th><label for="field-tbl"><?php echo Lang::txt('COM_GROUPS_TO'); ?>:</label></th>
						<td>
							<select name="tbl" id="field-tbl">
								<option value="invitees"><?php echo Lang::txt('COM_GROUPS_INVITEES'); ?></option>
								<option value="applicants"><?php echo Lang::txt('COM_GROUPS_APPLICANTS'); ?></option>
								<option value="members" selected="selected"><?php echo Lang::txt('COM_GROUPS_MEMBERS'); ?></option>
								<option value="managers"><?php echo Lang::txt('COM_GROUPS_MANAGERS'); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<?php echo Html::input('token'); ?>
</form>
