<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Support\Helpers\Permissions::getActions('category');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_CATEGORIES') . ': ' . $text, 'support');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js('edit.js');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_SUPPORT_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" class="required" value="<?php echo $this->escape($this->row->get('title')); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS'); ?>:</label>
					<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape($this->row->get('alias')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_SUPPORT_FIELD_ALIAS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_SUPPORT_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id'); ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->get('id')); ?>" />
						</td>
					</tr>
				<?php if ($this->row->get('created_by')) { ?>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_SUPPORT_FIELD_CREATED'); ?>:</th>
						<td>
							<time datetime="<?php echo $this->row->get('created'); ?>"><?php echo Date::of($this->row->get('created'))->toLocal('Y-m-d H:i:s'); ?></time>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_SUPPORT_FIELD_CREATOR'); ?>:</th>
						<td>
							<?php
							$user = User::getInstance($this->row->get('created_by'));
							echo $this->escape($user->get('name'));
							?>
						</td>
					</tr>
					<?php if ($this->row->get('modified_by') && $this->row->get('modified_by') != '0000-00-00 00:00:00') { ?>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_SUPPORT_FIELD_MODIFIED'); ?>:</th>
							<td>
								<time datetime="<?php echo $this->row->get('modified'); ?>"><?php echo Date::of($this->row->get('modified'))->toLocal('Y-m-d H:i:s'); ?></time>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_SUPPORT_FIELD_MODIFIER'); ?>:</th>
							<td>
								<?php
								$user = User::getInstance($this->row->get('modified_by'));
								echo $this->escape($user->get('name'));
								?>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>