<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$tmpl = Request::getCmd('tmpl', '');

$canDo = \Components\Groups\Helpers\Permissions::getActions();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_ROLES') . ': ' . $text);
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('edit');

Html::behavior('framework');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration">
			<div class="fltrt configuration-options">
				<button type="button" id="btn-save"><?php echo Lang::txt('JTOOLBAR_SAVE');?></button>
				<button type="button" id="btn-cancel"><?php echo Lang::txt('COM_GROUPS_MEMBER_CANCEL');?></button>
			</div>
			<?php echo Lang::txt('COM_GROUPS_ROLES') . ': ' . $text; ?>
		</div>
	</fieldset>
	<fieldset id="filter-bar" class="filter clearfix">
	</fieldset>
<?php } ?>
	<div class="grid">
	<div class="col span7">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-name"><?php echo Lang::txt('COM_GROUPS_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="fields[name]" id="field-name" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->model->get('name'))); ?>" />
			</div>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_PERMISSIONS'); ?></span></legend>

				<?php $permissions = $this->model->permissions; ?>

				<div class="input-wrap">
					<input type="checkbox" name="fields[permissions][group.invite]" id="field-permissions-invite" value="1" <?php if ($permissions->get('group.invite') == 1) { echo 'checked="checked"'; } ?> />
					<label for="field-permissions-invite"><?php echo Lang::txt('COM_GROUPS_PERMISSION_INVITE'); ?></label>
				</div>

				<div class="input-wrap">
					<input type="checkbox" name="fields[permissions][group.edit]" id="field-permissions-edit" value="1" <?php if ($permissions->get('group.edit') == 1) { echo 'checked="checked"'; } ?> />
					<label for="field-permissions-edit"><?php echo Lang::txt('COM_GROUPS_PERMISSION_EDIT'); ?></label>
				</div>

				<div class="input-wrap">
					<input type="checkbox" name="fields[permissions][group.pages]" id="field-permissions-pages" value="1" <?php if ($permissions->get('group.pages') == 1) { echo 'checked="checked"'; } ?> />
					<label for="field-permissions-pages"><?php echo Lang::txt('COM_GROUPS_PERMISSION_PAGES'); ?></label>
				</div>
			</fieldset>
		</fieldset>
	</div>
	<div class="col span5">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_GROUPS_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->model->get('id'); ?>
						<input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />
						<input type="hidden" name="fields[gidNumber]" value="<?php echo $this->group->get('gidNumber'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<?php echo Html::input('token'); ?>
</form>