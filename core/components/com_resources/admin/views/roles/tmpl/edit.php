<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('role');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_ROLES') . ': ' . $text, 'resources');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="item-form" name="adminForm" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_RESOURCES_FIELD_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[title]" id="field-title" class="required" value="<?php echo $this->escape($this->row->title); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT'); ?>">
					<label for="field-alias"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS'); ?>:</label><br />
					<input type="text" name="fields[alias]" id="field-alias" value="<?php echo $this->escape($this->row->alias); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_RESOURCES_FIELD_ALIAS_HINT'); ?></span>
				</div>

				<input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="save" />
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_RESOURCES_FIELDSET_TYPES'); ?></span></legend>

				<?php
				if ($this->types)
				{
					$types = array();
					foreach ($this->row->types()->rows() as $t)
					{
						$types[] = $t->get('id');
					}

					foreach ($this->types as $type)
					{
						?>
						<div class="input-wrap">
							<input type="checkbox" name="types[]" id="type-<?php echo $type->id; ?>"<?php if (in_array($type->id, $types)) { echo ' checked="checked"'; } ?> value="<?php echo $type->id; ?>" />
							<label for="type-<?php echo $type->id; ?>"><?php echo $this->escape(stripslashes($type->type)); ?></label>
						</div>
						<?php
					}
				}
				?>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_RESOURCES_FIELD_ID'); ?></th>
						<td>
							<?php echo $this->row->id; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_RESOURCES_FIELD_CREATOR'); ?></th>
						<td>
							<?php
							$editor = User::getInstance($this->row->created_by);
							echo $this->escape($editor->get('name'));
							?>
							<input type="hidden" name="fields[created_by]" id="field-created_by" value="<?php echo $this->escape($this->row->created_by); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_RESOURCES_FIELD_CREATED'); ?></th>
						<td>
							<?php echo $this->row->created; ?>
							<input type="hidden" name="fields[created]" id="field-created" value="<?php echo $this->escape($this->row->created); ?>" />
						</td>
					</tr>
				<?php if ($this->row->modified_by) { ?>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_RESOURCES_FIELD_MODIFIER'); ?></th>
						<td>
							<?php
							$modifier = User::getInstance($this->row->modified_by);
							echo $this->escape($modifier->get('name'));
							?>
							<input type="hidden" name="fields[modified_by]" id="field-modified_by" value="<?php echo $this->escape($this->row->modified_by); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_RESOURCES_FIELD_MODIFIED'); ?></th>
						<td>
							<?php echo $this->row->modified; ?>
							<input type="hidden" name="fields[modified]" id="field-modified" value="<?php echo $this->escape($this->row->modified); ?>" />
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<?php echo Html::input('token'); ?>
</form>